<?php
require_once 'Peerforest/Ajax/controllers/IndexController.php';
class Jefferson_Provador_IndexController extends Peerforest_Ajax_IndexController
{
	public function addAction()
	{
		$cart   = $this->_getCart();
		$params = $this->getRequest()->getParams();
		if($params['isAjax'] == 1){
			$response = array();
			try {
				if (isset($params['qty'])) {
					$filter = new Zend_Filter_LocalizedToNormalized(
					array('locale' => Mage::app()->getLocale()->getLocaleCode())
					);
					$params['qty'] = $filter->filter($params['qty']);
				}

				$product = $this->_initProduct();

				/**
				 * Caso o produto seja adicionado através do catalogo
				 * as opções personalizada são adicionadas a variável
				 * $param
				 */
				$params = Mage::helper('provador')->setCustomOptions($params, $product);

				$related = $this->getRequest()->getParam('related_product');

				/**
				 * Check product availability
				 */
				if (!$product) {
					$response['status'] = 'ERROR';
					$response['message'] = $this->__('Unable to find Product ID');
				}

				$cart->addProduct($product, $params);
				if (!empty($related)) {
					$cart->addProductsByIds(explode(',', $related));
				}

				$cart->save();

				$this->_getSession()->setCartWasUpdated(true);

				/**
				 * @todo remove wishlist observer processAddToCart
				 */
				Mage::dispatchEvent('checkout_cart_add_product_complete',
				array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
				);

				if (!$cart->getQuote()->getHasError()){
					$message = '<p>' . $this->__('%s was added to your shopping cart.', Mage::helper('core')->htmlEscape($product->getName())) . '</p>';
					$response['status'] = 'SUCCESS';
					$response['message'] = $message;
					//New Code Here
					$this->loadLayout();
					$toplink = $this->getLayout()->getBlock('top.links')->toHtml();
					$sidebar_block = $this->getLayout()->getBlock('cart_sidebar');
					Mage::register('referrer_url', $this->_getRefererUrl());
					$sidebar = $sidebar_block->toHtml();
					$response['toplink'] = $toplink;
					$response['sidebar'] = $sidebar;
				}
			} catch (Mage_Core_Exception $e) {
				$msg = "";
				if ($this->_getSession()->getUseNotice(true)) {
					$msg = $e->getMessage();
				} else {
					$messages = array_unique(explode("\n", $e->getMessage()));
					foreach ($messages as $message) {
						//$msg .= $message.'<br/>';
					    $msg .= $message;
					}
				}

				$response['status'] = 'ERROR';
				$response['message'] = $msg;
			} catch (Exception $e) {
				$response['status'] = 'ERROR';
				$response['message'] = '<p>' . $this->__('Cannot add the item to shopping cart.') . '</p>';
				Mage::logException($e);
			}
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
			return;
		}else{
			return parent::addAction();
		}
	}
	public function optionsAction(){
		$productId = $this->getRequest()->getParam('product_id');
		// Prepare helper and params
		$viewHelper = Mage::helper('catalog/product_view');

		$params = new Varien_Object();
		$params->setCategoryId(false);
		$params->setSpecifyOptions(false);

		// Render page
		try {
			$viewHelper->prepareAndRender($productId, $this, $params);
		} catch (Exception $e) {
			if ($e->getCode() == $viewHelper->ERR_NO_PRODUCT_LOADED) {
				if (isset($_GET['store'])  && !$this->getResponse()->isRedirect()) {
					$this->_redirect('');
				} elseif (!$this->getResponse()->isRedirect()) {
					$this->_forward('noRoute');
				}
			} else {
				Mage::logException($e);
				$this->_forward('noRoute');
			}
		}
	}

	private function _sendError( $response )
	{
	    $this->getResponse()->setHeader('Content-type', 'application/json');
	    $this->getResponse()->setBody( (string) $this->getRequest()->getParam('callback') . '(' . Mage::helper('core')->jsonEncode($response) . ')' );
	}

	public function updateItemOptionsAction()
	{
	    $cart   = $this->_getCart();
	    $id = (int) $this->getRequest()->getParam('id');
	    $params = $this->getRequest()->getParams();

	    if (!isset($params['options'])) {
		$params['options'] = array();
	    }

	    if ($params['isAjax'] == 1) {
		$response = array('params' => $params);

		try {
		    if (isset($params['qty'])) {
			$filter = new Zend_Filter_LocalizedToNormalized(
			    array('locale' => Mage::app()->getLocale()->getLocaleCode())
			);
			$params['qty'] = $filter->filter($params['qty']);
		    }

		    $quoteItem = $cart->getQuote()->getItemById($id);
		    if (!$quoteItem) {
			$this->_sendError(array(
			    'status' => 'ERROR',
			    'message' => '<p>' . $this->__('Quote item is not found.'),
			)) . '</p>';
			return;
		    }

		    $item = $cart->updateItem($id, new Varien_Object($params));
		    if (is_string($item)) {
			$this->_sendError(array(
			    'status' => 'ERROR',
			    'message' => $item,
			));
			return;
		    }
		    if ($item->getHasError()) {
			Mage::throwException($item->getMessage());
			$this->_sendError(array(
			    'status' => 'ERROR',
			    'message' => $item->getMessage(),
			));
			return;
		    }

		    $related = $this->getRequest()->getParam('related_product');
		    if (!empty($related)) {
			$cart->addProductsByIds(explode(',', $related));
		    }

		    $cart->save();

		    $this->_getSession()->setCartWasUpdated(true);

		    Mage::dispatchEvent('checkout_cart_update_item_complete',
			array('item' => $item, 'request' => $this->getRequest(), 'response' => $this->getResponse())
		    );
		    if (!$this->_getSession()->getNoCartRedirect(true)) {
			if (!$cart->getQuote()->getHasError()){
			    $response['status'] = 'SUCCESS';
			    $response['message'] = '<p>' . $this->__('%s is updated in your shopping cart.', Mage::helper('core')->htmlEscape($item->getProduct()->getName())) . '</p>';
			    $this->loadLayout();

					$toplink = $this->getLayout()->getBlock('top.links')->toHtml();
					$sidebar_block = $this->getLayout()->getBlock('cart_sidebar');
					Mage::register('referrer_url', $this->_getRefererUrl());
					$sidebar = $sidebar_block->toHtml();
					$response['toplink'] = $toplink;
					$response['sidebar'] = $sidebar;
			}
		    }
		} catch (Mage_Core_Exception $e) {
		    $msg = "";
		    if ($this->_getSession()->getUseNotice(true)) {
			$msg = $e->getMessage();
		    } else {
			$messages = array_unique(explode("\n", $e->getMessage()));
			foreach ($messages as $message) {
			    $msg .= $message . '<br/>';
			}
		    }

		    $response['status'] = 'ERROR';
		    $response['message'] = $msg;
		} catch (Exception $e) {
		    $response['status'] = 'ERROR';
		    $response['message'] = '<p>' . $this->__('Cannot update the item.') . '</p>';
		    Mage::logException($e);
		}
		//$this->_sendJson($response);
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
		return;
	    } else {
		return parent::updateItemOptionsAction();
	    }

	}



	public function deleteAction(){
        $id = (int) $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $this->_getCart()->removeItem($id)
                  ->save();
                $this->_getSession()->setCartWasUpdated(true);
                if (!$this->_getCart()->getQuote()->getHasError()){
			$product = $this->_initProduct();
			$message = '<p>' . $this->__('Item is deleted from your shopping cart.'). '</p>';

                    $response['status'] = 'SUCCESS';
                    $response['message'] = $message;
                    //New Code Here
                    $this->loadLayout();
                    $toplink = $this->getLayout()->getBlock('top.links')->toHtml();
                    $sidebar_block = $this->getLayout()->getBlock('cart_sidebar');
                    Mage::register('referrer_url', $this->_getRefererUrl());
                    $sidebar = $sidebar_block->toHtml();

                    $response['toplink'] = $toplink;
                    $response['sidebar'] = $sidebar;

                }

                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
            return;
            } catch (Exception $e) {
                $this->_getSession()->addError('<p>' . $this->__('Cannot remove the item.') . '</p>');
                Mage::logException($e);
            }
        }

        }
}