<?php
/*
  $Id: google_base.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/ 
   

  class osC_GoogleBase_Admin {
    function getAvailableProducts($start='', $limit='') {
      global $osC_Database, $osC_Language, $osC_Currencies;
      
      $Qselect = $osC_Database->query('select p.products_id, p.products_type, p.products_sku, p.products_price, pd.products_name 
                                       from :table_products_attributes pa 
                                       inner join :table_products_attributes_values pav on pa.products_attributes_values_id = pav.products_attributes_values_id
                                       inner join :table_products_attributes_groups pag on (pav.products_attributes_groups_id = pag.products_attributes_groups_id and pag.products_attributes_groups_name = :groups_name)
                                       inner join :table_products p on pa.products_id = p.products_id
                                       inner join :table_products_description pd on (p.products_id = pd.products_id and pd.language_id = :language_id)
                                       where p.products_status = :products_status');
      $Qselect->bindTable(':table_products_attributes', TABLE_PRODUCTS_ATTRIBUTES);
      $Qselect->bindTable(':table_products_attributes_values', TABLE_PRODUCTS_ATTRIBUTES_VALUES);
      $Qselect->bindTable(':table_products_attributes_groups', TABLE_PRODUCTS_ATTRIBUTES_GROUPS);
      $Qselect->bindTable(':table_products', TABLE_PRODUCTS);
      $Qselect->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
      $Qselect->bindInt(':language_id', $osC_Language->getID());
      $Qselect->bindInt(':products_status', 1);
      $Qselect->bindValue(':groups_name', 'Google Base');
      
      if (!empty($limit)) {
        $Qselect->setExtBatchLimit($start, $limit);
      }
      
      $Qselect->execute();
      
      $available_products = array();
      while($Qselect->next()) {
        $products_price = $osC_Currencies->format($Qselect->valueInt('products_price'));
        
        $products_type = $Qselect->valueInt('products_type');
        if ($products_type == PRODUCT_TYPE_GIFT_CERTIFICATE) {
          $Qcertificate = $osC_Database->query('select open_amount_min_value, open_amount_max_value from :table_products_gift_certificates where gift_certificates_amount_type = :gift_certificates_amount_type and products_id = :products_id');
          $Qcertificate->bindTable(':table_products_gift_certificates', TABLE_PRODUCTS_GIFT_CERTIFICATES);
          $Qcertificate->bindInt(':gift_certificates_amount_type', GIFT_CERTIFICATE_TYPE_OPEN_AMOUNT);
          $Qcertificate->bindInt(':products_id', $Qselect->valueInt('products_id'));
          $Qcertificate->execute();
          
          if ($Qcertificate->numberOfRows() > 0) {
            $products_price = $osC_Currencies->format($Qcertificate->value('open_amount_min_value')) . ' ~ ' . $osC_Currencies->format($Qcertificate->value('open_amount_max_value'));
          }
        }
        
        switch($products_type) {
          case PRODUCT_TYPE_SIMPLE:
            $products_type = 'Simple Product';
            break;
          case PRODUCT_TYPE_VIRTUAL:
            $products_type = 'Virtual Product';
            break;
          case PRODUCT_TYPE_DOWNLOADABLE:
            $products_type = 'Downloadable Product';
            break;  
        }
        
        $available_products[] = array('products_id' => $Qselect->valueInt('products_id'), 
                                      'products_name' => $Qselect->value('products_name'),
                                      'products_type' => $products_type, 
                                      'products_sku' => $Qselect->value('products_sku'), 
                                      'products_price' => $products_price);
      }
      
      if (!empty($limit)) {
        $total = $Qselect->getBatchSize();
      }else {
        $total = 0;
      }
      
      $records = array('total' => $total, 'available_products' => $available_products);
      
      return $records;
    }
    
    function clientLoginContentApi() {
      $url = 'https://www.google.com/accounts/ClientLogin';
      $header = array('Content-type: application/x-www-form-urlencoded');
      
      $post_params = array('accountType' => GOOGLE_BASE_ACCOUNT_TYPE, 
                           'Email' => GOOGLE_BASE_ACCOUNT_NAME, 
                           'Passwd' => GOOGLE_BASE_ACCOUNT_PASSWORD, 
                           'service' => 'structuredcontent', 
                           'source' => 'elootec-tomatocart-1.2.0alpha3');
      
      $post_string = '';
      foreach($post_params as $param => $value) {
        $post_string .= urlencode($param) . '=' . urlencode($value) . '&';
      }
      
      $post_string = substr($post_string, 0, -1);
      
      $response = self::sendRequestToGoogleBase($url, $post_string, $header);
      
      if (preg_match('/Auth=[\w\W]*/', $response, $matches)) {
        $token = str_replace('Auth=', '', $matches[0]);
        
        $_SESSION['gContentToken'] = $token;
      }        
    }
    
    function clientLoginSearch() {
      $url = 'https://www.google.com/accounts/ClientLogin';
      $header = array('Content-type: application/x-www-form-urlencoded');
      
      $post_params = array('accountType' => GOOGLE_BASE_ACCOUNT_TYPE, 
                           'Email' => GOOGLE_BASE_ACCOUNT_NAME, 
                           'Passwd' => GOOGLE_BASE_ACCOUNT_PASSWORD, 
                           'service' => 'shoppingapi', 
                           'source' => 'elootec-tomatocart-1.2.0alpha3');
      
      $post_string = '';
      foreach($post_params as $param => $value) {
        $post_string .= urlencode($param) . '=' . urlencode($value) . '&';
      }
      
      $post_string = substr($post_string, 0, -1);
      
      $response = self::sendRequestToGoogleBase($url, $post_string, $header);
      
      if (preg_match('/Auth=[\w\W]*/', $response, $matches)) {
        $token = str_replace('Auth=', '', $matches[0]);
        
        $_SESSION['gSearchToken'] = $token;
      }        
    }
    
    function synchronous() {
      $url = 'https://www.googleapis.com/shopping/search/v1/public/products/' . GOOGLE_BASE_ACCOUNT_ID . '/gid/13?key=AIzaSyBa3TBJK8LEQFDswbzSWE2yOd-e3XpXkVY';
      
      $header = array('Authorization: GoogleLogin Auth=' . trim($_SESSION['gSearchToken']));
      
      $response = self::sendRequestToGoogleBase($url, '', $header, 'get');
      
      return $response;
    }
    
    function uploadSingleProduct($products_id) {
      global $osC_Database, $osC_Language, $osC_Currencies;
      
      $Qselect = $osC_Database->query('select p.*, pd.*, pi.image, pa.value
                                       from :table_products_attributes pa 
                                       inner join :table_products_attributes_values pav on (pa.products_attributes_values_id = pav.products_attributes_values_id and pa.products_id = :products_id)
                                       inner join :table_products_attributes_groups pag on (pav.products_attributes_groups_id = pag.products_attributes_groups_id and pag.products_attributes_groups_name = :groups_name and pav.name = :condition)
                                       inner join :table_products p on pa.products_id = p.products_id
                                       inner join :table_products_description pd on (p.products_id = pd.products_id and pd.language_id = :language_id)
                                       inner join :table_products_images pi on (p.products_id = pi.products_id)
                                       where p.products_status = :products_status');
      
      $Qselect->bindTable(':table_products_attributes', TABLE_PRODUCTS_ATTRIBUTES);
      $Qselect->bindTable(':table_products_attributes_values', TABLE_PRODUCTS_ATTRIBUTES_VALUES);
      $Qselect->bindTable(':table_products_attributes_groups', TABLE_PRODUCTS_ATTRIBUTES_GROUPS);
      $Qselect->bindTable(':table_products', TABLE_PRODUCTS);
      $Qselect->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
      $Qselect->bindTable(':table_products_images', TABLE_PRODUCTS_IMAGES);
      $Qselect->bindInt(':language_id', $osC_Language->getID());
      $Qselect->bindInt(':products_status', 1);
      $Qselect->bindInt(':products_id', $products_id);
      $Qselect->bindValue(':groups_name', 'Google Base');
      $Qselect->bindValue(':condition', 'Condition');
      
      $Qselect->execute();
      
      $product = array();
      while($Qselect->next()) {
        $Qproduct_category = $osC_Database->query('SELECT pa.value FROM (SELECT * FROM :table_products_attributes_groups WHERE products_attributes_groups_name = :products_attributes_groups_name) AS pag 
                                                  INNER JOIN :table_products_attributes_values pav ON pag.products_attributes_groups_id = pav.products_attributes_groups_id
                                                  INNER JOIN :table_products_attributes pa ON ( pav.products_attributes_values_id = pa.products_attributes_values_id and pav.language_id = pa.language_id)
                                                  WHERE pav.name =  :products_attributes_name and pav.status = :products_attributes_status and pa.products_id = :products_id and pa.language_id = :language_id');
        $Qproduct_category->bindTable(':table_products_attributes_groups', TABLE_PRODUCTS_ATTRIBUTES_GROUPS);
        $Qproduct_category->bindTable(':table_products_attributes_values', TABLE_PRODUCTS_ATTRIBUTES_VALUES);
        $Qproduct_category->bindTable(':table_products_attributes', TABLE_PRODUCTS_ATTRIBUTES);
        $Qproduct_category->bindValue(':products_attributes_groups_name', 'Google Base');
        $Qproduct_category->bindValue(':products_attributes_name', 'Product Category');
        $Qproduct_category->bindInt(':products_attributes_status', 1);
        $Qproduct_category->bindInt(':products_id', $products_id);
        $Qproduct_category->bindInt(':language_id', $osC_Language->getID());
        $Qproduct_category->execute();
        
        $product_category = $Qproduct_category->toArray();
        
        $lan_code = $osC_Language->getCode();
        $lan_country = explode('_', $lan_code);
        $lanuage = $lan_country[0];
        $country = $lan_country[1];
        
        $condition = array('new', 'used', 'refurbished');
        
        $product = array( 'title' => $Qselect->value('products_name'), 
                          'description' => $Qselect->value('products_description'),
                          'link' => 'http://test.tomatocart.com/' . FILENAME_PRODUCTS . '?' . $products_id,
                          'id' => $Qselect->value('products_id'), 
                          'image_link' => 'http://test.tomatocart.com/' . DIR_WS_IMAGES . '/products/large/' . $Qselect->value('image'), 
                          'content_language' => $lanuage, 
                          'target_country' => $country, 
                          'condition' => $condition[$Qselect->valueInt('value')], 
                          'google_product_category' => urlencode($product_category['value']),
                          'price' => array('price' => $Qselect->valueMixed('products_price', 'decimal'), 'unit' => isset($_SESSION['currency']) ? $_SESSION['currency'] : DEFAULT_CURRENCY)); 
        
        break;
      }
      
      $Qselect->freeResult();
      
      $product_xml = "<?xml version='1.0' encoding='UTF-8'?>";
      $product_xml .= '<entry xmlns="http://www.w3.org/2005/Atom" xmlns:sc="http://schemas.google.com/structuredcontent/2009" xmlns:scp="http://schemas.google.com/structuredcontent/2009/products" >' .
                        '<title type="text">' . $product['title'] . '</title>' .
                        '<link rel="alternate" type="text/html" href="' . $product['link'] . '"/>' .
                        '<content type="text">' . $product['description'] . '</content>' .
                        '<sc:id>' . $product['id'] . '</sc:id>' .
                        '<sc:image_link>'. $product['image_link'] . '</sc:image_link>' .
                        '<sc:content_language>' . $product['content_language'] . '</sc:content_language>' . 
                        '<sc:target_country>' . $product['target_country'] . '</sc:target_country>' .
                        '<scp:google_product_category>' . $product['google_product_category'] . '</scp:google_product_category>' .
                        '<scp:price unit="' . $product['price']['unit'] . '">' . $product['price']['price'] . '</scp:price>' .
                        '<scp:condition>' . $product['condition'] . '</scp:condition>' .
                      '</entry>';
      
      $product_xml = str_replace("<br>", '&lt;/br&gt;', $product_xml);
      
      $url = 'https://content.googleapis.com/content/v1/' . GOOGLE_BASE_ACCOUNT_ID . '/items/products/schema';
      
      $header_auth = 'Authorization: GoogleLogin Auth=' . trim($_SESSION['gContentToken']);
      $header_content_len = 'Content-length: ' . strlen($product_xml);
      
      $header = array($header_auth,
                      $header_content_len,
                      'Content-type: application/atom+xml');
      
      $response = self::sendRequestToGoogleBase($url, $product_xml, $header);
      
      if (preg_match('/<sc:id>/', $response)) {
        $sxml = simplexml_load_string($response);
        
        $gitem_name = $sxml->title;
        
        $sc_childrens = $sxml->children('http://schemas.google.com/structuredcontent/2009');
        $gitem_base_id = $sc_childrens->id;
        $gitem_target_country = $sc_childrens->target_country;
        $gitem_expires = substr($sc_childrens->expiration_date, 0, 10);
        
        $gitem_links = $sxml->link;
        foreach($gitem_links as $gitem_link) {
          foreach($gitem_link->attributes() as $attr_key => $attr_value) {
            if ($attr_key == 'rel' && $attr_value == 'edit') {
              $gitem_edit_link = $gitem_link;
              
              break 2;
            }
          }
        }
        
        if (!empty($gitem_edit_link)) {
          foreach($gitem_edit_link->attributes() as $attr_key=> $attr_value) {
            if ($attr_key == 'href') {
              $gitem_link = $attr_value;
              
              break;
            }
          }
        }
        
        $Qinsert = $osC_Database->query('insert into :table_google_base_items (google_base_id, items_name, items_target_country, items_expires, items_edit_link)
                                         values (:google_base_id, :items_name, :items_target_country, :items_expires, :items_edit_link)');
        
        $Qinsert->bindTable(':table_google_base_items', TABLE_GOOGLE_BASE_ITEMS);
        $Qinsert->bindValue(':google_base_id', $gitem_base_id);
        $Qinsert->bindValue(':items_name', $gitem_name);
        $Qinsert->bindValue(':items_target_country', $gitem_target_country);
        $Qinsert->bindValue(':items_expires', $gitem_expires);
        $Qinsert->bindValue(':items_edit_link', $gitem_link);
        $Qinsert->execute();
        
        return true;
      }else {
        return false;
      }
    }
    
    function getManageItems($start, $limit) {
      global $osC_Database;
      
      $Qselect = $osC_Database->query('select gbi.google_base_id, gbi.items_name, gbi.items_target_country, gbi.items_expires, gbi.items_edit_link, gbi.impressions, gbi.clicks, c.countries_name from :table_google_base_items gbi inner join :table_countries c on gbi.items_target_country = c.countries_iso_code_2 order by gbi.items_expires');
      $Qselect->bindTable(':table_google_base_items', TABLE_GOOGLE_BASE_ITEMS);
      $Qselect->bindTable(':table_countries', TABLE_COUNTRIES);
      $Qselect->setExtBatchLimit($start, $limit);
      $Qselect->execute();
      
      $items = array();
      while($Qselect->next()) {
        $items[] = array( 'google_base_id' => $Qselect->value('google_base_id'), 
                          'items_name' => $Qselect->value('items_name'), 
                          'items_target_country' => $Qselect->value('countries_name'), 
                          'items_expires' => $Qselect->value('items_expires'), 
                          'items_edit_link' => $Qselect->value('items_edit_link'),
                          'impressions' => $Qselect->valueInt('impressions'), 
                          'clicks' => $Qselect->valueInt('clicks'));
      }
      
      return array('total' => $Qselect->getBatchSize(), 'items' => $items);
    }
    
    function deleteProduct($item_id) {
      global $osC_Database;
      
      $Qselect = $osC_Database->query('select google_base_id, items_edit_link from :table_google_base_items where google_base_id = :google_base_id');
      $Qselect->bindTable(':table_google_base_items', TABLE_GOOGLE_BASE_ITEMS);
      $Qselect->bindInt(':google_base_id', $item_id);
      $Qselect->execute();
      
      $product_info = $Qselect->toArray();
      
      $product_link = $product_info['items_edit_link'];
      
      if ( !isset($_SESSION['gContentToken']) && empty($_SESSION['gContentToken']) ) {
        osC_GoogleBase_Admin::clientLoginContentApi();
      }
      
      $header = array('Authorization: GoogleLogin Auth=' . trim($_SESSION['gContentToken']));
      
      self::sendRequestToGoogleBase($product_link, '', $header, 'delete');
     
      $Qdel = $osC_Database->query('delete from :table_google_base_items where google_base_id = :google_base_id');
      $Qdel->bindTable(':table_google_base_items', TABLE_GOOGLE_BASE_ITEMS);
      $Qdel->bindInt(':google_base_id', $item_id);
      $Qdel->execute();
      
      if ($Qdel->affectedRows() > 0) {
        return true;
      }else {
        return false;
      }
    }
    
    function sendRequestToGoogleBase($url, $parameters, $header = '', $method = 'post', $certificate = '') {
      if (empty($header) || !is_array($header)) {
        $header = array();
      }

      $server = parse_url($url);

      if (isset($server['port']) === false) {
        $server['port'] = ($server['scheme'] == 'https') ? 443 : 80;
      }

      if (isset($server['path']) === false) {
        $server['path'] = '/';
      }

      if (isset($server['user']) && isset($server['pass'])) {
        $header[] = 'Authorization: Basic ' . base64_encode($server['user'] . ':' . $server['pass']);
      }

      $connection_method = 0;

      if (function_exists('curl_init')) {
        $connection_method = 1;
      } elseif ( ($server['scheme'] == 'http') || (($server['scheme'] == 'https') && extension_loaded('openssl')) ) {
        if (function_exists('stream_context_create')) {
          $connection_method = 3;
        } else {
          $connection_method = 2;
        }
      }

      $result = '';

      switch ($connection_method) {
        case 1:
          $curl = curl_init($server['scheme'] . '://' . $server['host'] . $server['path'] . (isset($server['query']) ? '?' . $server['query'] : ''));
          curl_setopt($curl, CURLOPT_PORT, $server['port']);

          if (!empty($header)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
          }

          if (!empty($certificate)) {
            curl_setopt($curl, CURLOPT_SSLCERT, $certificate);
          }

          curl_setopt($curl, CURLOPT_HEADER, 0);
          curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
          curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
          curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
          
          if ($method == 'post' && !empty($parameters)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $parameters);
          }else if ($method == 'get') {
            curl_setopt($curl, CURLOPT_HTTPGET, 1);
          }else if ($method == 'delete') {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST , 'delete');
          }
          
          $result = curl_exec($curl);
          
          curl_close($curl);
          
          break;
        case 2:
          if ($fp = @fsockopen(($server['scheme'] == 'https' ? 'ssl' : $server['scheme']) . '://' . $server['host'], $server['port'])) {
            @fputs($fp, 'POST ' . $server['path'] . (isset($server['query']) ? '?' . $server['query'] : '') . ' HTTP/1.1' . "\r\n" .
                        'Host: ' . $server['host'] . "\r\n" .
                        'Content-type: application/x-www-form-urlencoded' . "\r\n" .
                        'Content-length: ' . strlen($parameters) . "\r\n" .
                        (!empty($header) ? implode("\r\n", $header) . "\r\n" : '') .
                        'Connection: close' . "\r\n\r\n" .
                        $parameters . "\r\n\r\n");

            $result = @stream_get_contents($fp);

            @fclose($fp);

            $result = trim(substr($result, strpos($result, "\r\n\r\n", strpos(strtolower($result), 'content-length:'))));
          }

          break;

        case 3:
          $options = array('http' => array('method' => 'POST',
                                           'header' => 'Host: ' . $server['host'] . "\r\n" .
                                                       'Content-type: application/x-www-form-urlencoded' . "\r\n" .
                                                       'Content-length: ' . strlen($parameters) . "\r\n" .
                                                       (!empty($header) ? implode("\r\n", $header) . "\r\n" : '') .
                                                       'Connection: close',
                                           'content' => $parameters));

          if (!empty($certificate)) {
            $options['ssl'] = array('local_cert' => $certificate);
          }

          $context = stream_context_create($options);

          if ($fp = fopen($url, 'r', false, $context)) {
            $result = '';

            while (!feof($fp)) {
              $result .= fgets($fp, 4096);
            }

            fclose($fp);
          }

          break;

        default:
          exec(escapeshellarg(CFG_APP_CURL) . ' -d ' . escapeshellarg($parameters) . ' "' . $server['scheme'] . '://' . $server['host'] . $server['path'] . (isset($server['query']) ? '?' . $server['query'] : '') . '" -P ' . $server['port'] . ' -k' . (!empty($header) ? ' -H ' . escapeshellarg(implode("\r\n", $header)) : '') . (!empty($certificate) ? ' -E ' . escapeshellarg($certificate) : ''), $result);
          $result = implode("\n", $result);
      }

      return $result;
    }
  } 
?>