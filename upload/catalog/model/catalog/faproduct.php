<?php
class ModelCatalogFAproduct extends Model {

	public function getFAproducts($id)  {
		$query = $this->db->query("SELECT `product_id` FROM `" . DB_PREFIX . "faproduct_to_facategory` WHERE `facategory_id` = (SELECT DISTINCT `facategory_id` FROM `" . DB_PREFIX . "facategory_to_faproduct` WHERE `product_id` = ".$id.") AND `product_id` != ".$id);
	
		return $query->rows;
	}
}
?>