<?php
require_once('../../../wp-load.php');
$product = wc_get_product( get_page_by_path( 'dnk-black-shoes', OBJECT, 'product' )->ID );
echo "Full image HTML:\n";
echo trim($product->get_image( 'full', array( 'class' => 'test-class' ) ));
echo "\nThumb image HTML:\n";
echo trim($product->get_image( 'woocommerce_thumbnail', array( 'class' => 'test-class' ) ));
echo "\nCSS object-contain check:\n";
?>
