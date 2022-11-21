<?php
class ModelLocalisationZone extends Model {

				public function getZonesByEstado($estado) {
					if(strlen($estado) <= 2){
						$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone WHERE country_id = '30' AND code = '".$estado."' AND status = '1' ORDER BY name");
						$zone_id = $query->row['zone_id'];
					}else{
						$zone_id = '';
					}
					return $zone_id;
				}
			

				public function getZonesByEstadocep($estado) {
					if(strlen($estado) <= 2){
						$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone WHERE country_id = '30' AND code = '".$estado."' AND status = '1' ORDER BY name");
						$zone_id = $query->row['zone_id'];
					}else{
						$zone_id = '';
					}
					return $zone_id;
				}
			
	public function getZone($zone_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$zone_id . "' AND status = '1'");

		return $query->row;
	}

	public function getZonesByCountryId($country_id) {
		$zone_data = $this->cache->get('zone.' . (int)$country_id);

		if (!$zone_data) {
			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE country_id = '" . (int)$country_id . "' AND status = '1' ORDER BY name");

			$zone_data = $query->rows;

			$this->cache->set('zone.' . (int)$country_id, $zone_data);
		}

		return $zone_data;
	}
}
