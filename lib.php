<?php
readonly class Products
{
    public function __construct(
        private string $xml_file_path = ''
    ) {
    }

    /**
     * This function prints an HTML table with all the products as read from the xml file
     */
    public function print_html_table_with_all_products(): void
    {
        $xml_data = simplexml_load_file($this->xml_file_path);

        if ($xml_data === false) {
            echo '<p>Failed to load ' . htmlspecialchars($this->xml_file_path) . '</p>';
            return;
        }

        echo '<table border="1" cellpadding="5" cellspacing="0">';
        echo '<thead><tr>';
        echo '<th>NAME</th>';
        echo '<th>PRICE</th>';
        echo '<th>QUANTITY</th>';
        echo '<th>CATEGORY</th>';
        echo '<th>MANUFACTURER</th>';
        echo '<th>BARCODE</th>';
        echo '<th>WEIGHT</th>';
        echo '<th>INSTOCK</th>';
        echo '<th>AVAILABILITY</th>';
        echo '</tr></thead>';
        echo '<tbody>';

        foreach ($xml_data->PRODUCTS->PRODUCT as $prod) {
            $this->print_html_of_one_product_line($prod);
        }

        echo '</tbody>';
        echo '</table>';
    }

    /**
     * This function prints an HTML tr for a given product
     */
    private function print_html_of_one_product_line(object $prod): void
    {
        echo '<tr>';
        echo '<td>' . $this->clean($prod->NAME) . '</td>';
        echo '<td>' . $this->clean($prod->PRICE) . '</td>';
        echo '<td>' . $this->clean($prod->QUANTITY) . '</td>';
        echo '<td>' . $this->clean($prod->CATEGORY) . '</td>';
        echo '<td>' . $this->clean($prod->MANUFACTURER) . '</td>';
        echo '<td>' . $this->clean($prod->BARCODE) . '</td>';
        echo '<td>' . $this->clean($prod->WEIGHT) . '</td>';
        echo '<td>' . $this->clean($prod->INSTOCK) . '</td>';
        echo '<td>' . $this->clean($prod->AVAILABILITY) . '</td>';
        echo '</tr>';
    }

    private function clean(mixed $value): string
    {
        return htmlspecialchars(trim((string) $value));
    }

    /**
     * Add a new PRODUCT to the xml file
     *
     * @param  array<array-key, mixed> $data
     *
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \DOMException
     */
    public function add_product_to_xml(array $data): void
    {
        $this->validate_new_product($data);

        $name = trim((string) ($data['NAME'] ?? ''));

        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;

        if (@$dom->load($this->xml_file_path) === false) {
            throw new RuntimeException('Failed to load ' . $this->xml_file_path);
        }

        $products_list = $dom->getElementsByTagName('PRODUCTS')->item(0);

        if ($products_list === null) {
            throw new RuntimeException('The xml file has no PRODUCTS element.');
        }

        $product = $dom->createElement('PRODUCT');

        $product->appendChild($this->create_cdata_element($dom, 'NAME', $name));
        $product->appendChild($dom->createElement('PRICE', trim((string) ($data['PRICE'] ?? ''))));
        $product->appendChild($dom->createElement('QUANTITY', trim((string) ($data['QUANTITY'] ?? ''))));

        $category = $dom->createElement('CATEGORY');
        $category->appendChild($dom->createTextNode(trim((string) ($data['CATEGORY'] ?? ''))));
        $category_id = trim((string) ($data['CATEGORY_ID'] ?? ''));

        if ($category_id !== '') {
            $category->setAttribute('id', $category_id);
        }

        $product->appendChild($category);
        $product->appendChild($dom->createElement('MANUFACTURER', trim((string) ($data['MANUFACTURER'] ?? ''))));
        $product->appendChild($this->create_cdata_element($dom, 'BARCODE', trim((string) ($data['BARCODE'] ?? ''))));
        $product->appendChild($this->create_cdata_element($dom, 'WEIGHT', trim((string) ($data['WEIGHT'] ?? ''))));
        $product->appendChild($dom->createElement('INSTOCK', ($data['INSTOCK'] ?? 'N')));
        $product->appendChild($dom->createElement('AVAILABILITY', trim((string) ($data['AVAILABILITY'] ?? ''))));

        $products_list->appendChild($product);

        $last_update = $dom->getElementsByTagName('LAST_UPDATE')->item(0);

        if ($last_update !== null) {
            $last_update->nodeValue = date('Y-m-d H:i');
        }

        if (file_put_contents($this->xml_file_path, $dom->saveXML(), LOCK_EX) === false) {
            throw new RuntimeException('Failed to write ' . $this->xml_file_path);
        }
    }

    /**
     * Create CDATA element
     *
     * @throws \DOMException
     */
    private function create_cdata_element(DOMDocument $dom, string $tag, string $value): DOMElement
    {
        $element = $dom->createElement($tag);
        $element->appendChild($dom->createCDATASection($value));

        return $element;
    }

    /**
     * Validate the posted data of a new product
     *
     * Name is required
     *
     * @param  array<array-key, mixed> $data
     *
     * @throws InvalidArgumentException
     */
    private function validate_new_product(array $data): void
    {
        $errors = [];

        if (trim((string) ($data['NAME'] ?? '')) === '') {
            $errors[] = 'The product name is required.';
        }

        $price = trim((string) ($data['PRICE'] ?? ''));
        if ($price !== '' && (!is_numeric($price) || (float) $price < 0)) {
            $errors[] = 'The price must be a positive number.';
        }

        $quantity = trim((string) ($data['QUANTITY'] ?? ''));
        if ($quantity !== '' && (!ctype_digit($quantity))) {
            $errors[] = 'The quantity must be a positive whole number.';
        }

        $category_id = trim((string) ($data['CATEGORY_ID'] ?? ''));
        if ($category_id !== '' && !ctype_digit($category_id)) {
            $errors[] = 'The category id must be a number.';
        }

        $instock = trim((string) ($data['INSTOCK'] ?? 'N'));
        if (!in_array($instock, ['Y', 'N'], true)) {
            $errors[] = 'In stock must be either Y or N.';
        }

        if ($errors !== []) {
            throw new InvalidArgumentException(implode(' ', $errors));
        }
    }
}
