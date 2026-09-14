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
}
