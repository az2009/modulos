<?php
class Jbp_Giftlist_Model_Autocomplete extends Mage_Core_Model_Abstract {

    /**
     * Retorna as cidades do estado informado
     * @param unknown $cod_estados
     */
    public function getCidades($cod_estados){
        $con = Mage::getSingleton('core/resource');
        $read = $con->getConnection('core_read');
        $sql = $read->select()
        ->from($con->getTableName('cidades'))
        ->where('estados_cod_estados=?', $cod_estados)
        ->order('nome');
        $data = $read->fetchAll($sql);
        foreach($data as $row){
            $cidades[] = array(
                'cod_cidades'	=> $row['cod_cidades'],
                'nome'			=> $row['nome'],
            );
        }
        echo   Mage::helper('core')->jsonEncode($cidades);
    }

}
