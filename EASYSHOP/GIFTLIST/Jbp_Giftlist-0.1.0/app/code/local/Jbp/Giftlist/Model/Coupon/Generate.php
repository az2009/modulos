<?php
class Jbp_Giftlist_Model_Coupon_Generate extends Mage_Core_Model_Abstract {
    
    
    /**
     * Gera a regra do carrinho 
     * @param unknown $saldoRestante
     * @param unknown $user
     * @param unknown $listId
     */
    public function generateRule($saldoRestante, $user = null, $listId = null){
            $name = "Resgate saldo lista de casamento: {$listId}| Usuário: {$user}"; // nome da regra do carrinho
            $websiteId = array(1);
            $customerGroupId = array(0,1,2,3);
            $actionType = 'cart_fixed'; // discount by percentage (other options are: by_fixed, cart_fixed, buy_x_get_y)
            $discount = $saldoRestante; // percentage discount
            //$sku = 'chair'; // product sku
            
            $shoppingCartPriceRule = Mage::getModel('salesrule/rule');
            
            $shoppingCartPriceRule
            ->setName($name)
            ->setDescription('Resgate da lista de casamento ')
            ->setIsActive(1)
            ->setWebsiteIds($websiteId)
            ->setCustomerGroupIds($customerGroupId)
            ->setFromDate('')
            ->setData('cupom_lista_casamento','list_marriage')
            ->setToDate(date('Y-m-d', strtotime("+6 month",  time())))
            ->setData('uses_per_customer',1)
            ->setSortOrder('')
            ->setSimpleAction($actionType)
            ->setDiscountAmount($discount)
            ->setData('stop_rules_processing',1)
            ->setData('coupon_type',2)
            ->setData('uses_per_coupon',1)
            ->setData('rule_use_auto_generation',1)
            ->setData('use_auto_generation',1)
            ->setStopRulesProcessing(0);
        
//         $skuCondition = Mage::getModel('salesrule/rule_condition_product')
//         ->setType('salesrule/rule_condition_product')
//         ->setAttribute('sku')
//         ->setOperator('==')
//         ->setValue($sku);
        
        try {
            //$shoppingCartPriceRule->getConditions()->addCondition($skuCondition);
            $shoppingCartPriceRule->save();
            return $shoppingCartPriceRule->getData('rule_id');
            
        } catch (Exception $e) {
            Mage::log($e->getMessage());
            return;
        }
    }
    
    /**
     * Gera o código de cupom da regra do carrinho
     * @param unknown $ruleId
     */
    public function generatorCoupon($ruleId){
            
        $generator = Mage::getModel('salesrule/coupon_massgenerator');
        $data = array(
            'uses_per_customer' => 1,
            'uses_per_coupon'   => 1,
            'qty'               => 1,  //number of coupons to generate
            'length'            => 16, //length of coupon string
            'to_date'           => date('Y-m-d', strtotime("+1 month",  time())), //ending date of generated promo
            'prefix'            => 'rev',
            'suffix'            => 'tf',
            'dash'              => 5,
            /**
             * Possible values include:
             * Mage_SalesRule_Helper_Coupon::COUPON_FORMAT_ALPHANUMERIC
             * Mage_SalesRule_Helper_Coupon::COUPON_FORMAT_ALPHABETICAL
             * Mage_SalesRule_Helper_Coupon::COUPON_FORMAT_NUMERIC
             */
            'format'          => Mage_SalesRule_Helper_Coupon::COUPON_FORMAT_ALPHANUMERIC,
            'rule_id'         => $ruleId //the id of the rule you will use as a template
        );
            
        try{
            $generator->validateData($data);
            $generator->setData($data);
            $generator->generatePool();
            
            $salesRule = Mage::getModel('salesrule/rule')->load($data['rule_id']);
            $collection = Mage::getResourceModel('salesrule/coupon_collection')
                            ->addRuleToFilter($salesRule)
                            ->addGeneratedCouponsFilter();
            return $collection;
            
        }catch(Exception $e){
            echo $e->getMessage();
        }
        
    }
    
    
}