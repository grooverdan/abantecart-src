<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2018 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

/** @noinspection PhpUndefinedClassInspection */
class ControllerResponsesProductProduct extends AController
{

    public $data = array();

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        try {
            $this->config->set('embed_mode', true);
            $cntr = $this->dispatch('pages/product/product');
            $html_out = $cntr->dispatchGetOutput();
        } catch (AException $e) {
        }

        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->response->setOutput($html_out);

    }

    public function is_group_option()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadModel('catalog/product');
        $group_options = $this->model_catalog_product->getProductGroupOptions(
            $this->request->get['product_id'],
            $this->request->get['option_id'],
            $this->request->get['option_value_id']
        );

        foreach ($group_options as $option_id => $option_values) {
            foreach ($option_values as $option_value_id => $option_value) {
                $name = $option_value['name'];
                $this->data['group_option'][$option_id][$option_value_id] = $name;
            }
        }

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->load->library('json');
        $this->response->addJSONHeader();
        $this->response->setOutput(AJson::encode($this->data['group_option']));
    }

    /*
     * Load images for product option
     * */
    public function get_option_resources()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $product_id = (int)$this->request->get['product_id'];
        $attribute_value_id = (int)$this->request->get['attribute_value_id'];
        $output = array();
        if ($attribute_value_id && is_int($attribute_value_id)) {
            $resource = new AResource('image');

            // main product image
            $msizes = array(
                'main'  =>
                    array(
                        'width'  => $this->config->get('config_image_popup_width'),
                        'height' => $this->config->get('config_image_popup_height'),
                    ),
                'thumb' => array(
                    'width'  => $this->config->get('config_image_thumb_width'),
                    'height' => $this->config->get('config_image_thumb_height'),
                ),
            );

            $output['main'] = $resource->getResourceAllObjects('product_option_value', $attribute_value_id, $msizes, 1, false);
            if (!$output['main']) {
                unset($output['main']);
            }

            // additional images
            $osizes = array(
                'main'   =>
                    array(
                        'width'  => $this->config->get('config_image_popup_width'),
                        'height' => $this->config->get('config_image_popup_height'),
                    ),
                'thumb'  =>
                    array(
                        'width'  => $this->config->get('config_image_additional_width'),
                        'height' => $this->config->get('config_image_additional_height'),
                    ),
                //product image zoom related thumbnail
                'thumb2' =>
                    array(
                        'width'  => $this->config->get('config_image_thumb_width'),
                        'height' => $this->config->get('config_image_thumb_height'),
                    ),
            );

            $output['images'] = $resource->getResourceAllObjects('product_option_value', $attribute_value_id, $osizes, 0, false);
            if (!$output['images']) {
                unset($output['images']);
            }

            //no image? return main product images
            if (!count($output) && $product_id) {
                $output['main'] = $resource->getResourceAllObjects('products', $product_id, $msizes, 1, false);
                if (!$output['main']) {
                    unset($output['main']);
                }
                $output['images'] = $resource->getResourceAllObjects('products', $product_id, $osizes, 0, false);
                if (!$output['images']) {
                    unset($output['images']);
                }
            }

        }
        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->load->library('json');
        $this->response->addJSONHeader();
        $this->response->setOutput(AJson::encode($output));
    }

    public function addToCart()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadModel('catalog/product');
        $product_info = $this->model_catalog_product->getProduct($this->request->get['product_id']);
        if ($product_info) {
            $this->cart->add($this->request->get['product_id'], ($product_info['minimum'] ? $product_info['minimum'] : 1));
        }

        $this->extensions->hk_UpdateData($this, __FUNCTION__);
        return $this->getCartContent();
    }

    public function getCartContent()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $display_totals = $this->cart->buildTotalDisplay();

        $dispatch = $this->dispatch('responses/product/product/get_cart_details', array($display_totals));

        $this->data['cart_details'] = $dispatch->dispatchGetOutput();
        $this->data['item_count'] = $this->cart->countProducts();

        $this->data['total'] = $this->currency->format($display_totals['total']);
        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->load->library('json');
        $this->response->addJSONHeader();
        $this->response->setOutput(AJson::encode($this->data));
    }

    public function get_cart_details($totals)
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        if (!$this->view->isTemplateExists('responses/checkout/cart_details.tpl')) {
            return '';
        }

        $cart_products = $this->cart->getProducts();

        $product_ids = array();
        foreach ($cart_products as $result) {
            $product_ids[] = (int)$result['product_id'];
        }

        $resource = new AResource('image');
        $thumbnails = $resource->getMainThumbList(
            'products',
            $product_ids,
            $this->config->get('config_image_product_width'),
            $this->config->get('config_image_product_height')
        );

        foreach ($cart_products as $result) {
            $option_data = array();
            $thumbnail = $thumbnails[$result['product_id']];
            foreach ($result['option'] as $option) {
                $value = $option['value'];
                // hide binary value for checkbox
                if ($option['element_type'] == 'C' && in_array($value, array(0, 1))) {
                    $value = '';
                }
                // strip long textarea value
                if ($option['element_type'] == 'T') {
                    $title = strip_tags($value);
                    $title = str_replace('\r\n', "\n", $title);

                    $value = str_replace('\r\n', "\n", $value);
                    if (mb_strlen($value) > 64) {
                        $value = mb_substr($value, 0, 64).'...';
                    }
                }

                $option_data[] = array(
                    'name'  => $option['name'],
                    'value' => $value,
                    'title' => $title,
                );
            }

            $qty += $result['quantity'];

            $this->data['products'][] = array(
                'key'      => $result['key'],
                'name'     => $result['name'],
                'option'   => $option_data,
                'quantity' => $result['quantity'],
                'stock'    => $result['stock'],
                'price'    => $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax'))),
                'href'     => $this->html->getSEOURL('product/product', '&product_id='.$result['product_id']),
                'thumb'    => $thumbnail,
            );
        }

        $this->data['totals'] = $totals['total_data'];
        $this->data['subtotal'] = $this->currency->format($this->tax->calculate($totals['total'], $result['tax_class_id'], $this->config->get('config_tax')));
        $this->data['taxes'] = $totals['taxes'];
        $this->data['view'] = $this->html->getURL('checkout/cart');

        $this->view->batchAssign($this->data);

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
        $this->processTemplate('responses/checkout/cart_details.tpl');
    }

    /*
     * Calculate product total based on options selected
     * */
    public function calculateTotal()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $output = array();
        //can not show price
        if (!$this->config->get('config_customer_price') && !$this->customer->isLogged()) {
            return $output;
        }

        if (has_value($this->request->post['product_id']) && is_numeric($this->request->post['product_id'])) {
            $product_id = $this->request->post['product_id'];
            if (isset($this->request->post['option'])) {
                $option = $this->request->post['option'];
            } else {
                $option = array();
            }

            if (isset($this->request->post['quantity'])) {
                $quantity = (int)$this->request->post['quantity'];
            } else {
                $quantity = 1;
            }
            $result = $this->cart->buildProductDetails($product_id, $quantity, $option);
            $output['total'] = $this->tax->calculate(
                $result['total'],
                $result['tax_class_id'],
                (int)$this->config->get('config_tax')
            );
            $output['price'] = $this->tax->calculate(
                $result['price'],
                $result['tax_class_id'],
                (int)$this->config->get('config_tax')
            );
            $output['total'] = $this->currency->format_total($output['price'], $quantity);
            $output['price'] = $this->currency->format($output['price']);
        }

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->load->library('json');
        $this->response->setOutput(AJson::encode($output));
    }

    public function editCartProduct()
    {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('checkout/cart');

        if (!empty($this->request->post['quantity'])) {
            foreach ($this->request->post['quantity'] as $key => $value) {
                $this->cart->update($key, $value);
            }
            unset($this->session->data['shipping_method']);
            unset($this->session->data['shipping_methods']);
            unset($this->session->data['payment_method']);
            unset($this->session->data['payment_methods']);
            $this->extensions->hk_UpdateData($this, __FUNCTION__);
        }
        return $this->getCartContent();
    }

    public function removeFromCart()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('checkout/cart');

        // Remove
        if ($this->request->post_or_get('key')) {
            $this->cart->remove($this->request->post_or_get('key'));
            $this->session->data['success'] = $this->language->get('text_remove');
            unset($this->session->data['shipping_method']);
            unset($this->session->data['shipping_methods']);
            unset($this->session->data['payment_method']);
            unset($this->session->data['payment_methods']);
            $this->extensions->hk_UpdateData($this, __FUNCTION__);
        }
        return $this->getCartContent();
    }
}