<?php

// проверка доменов на основную базу, если в "нежелательной тематике" - ахтунг

class DomainsInfo {

  private $siteIdsAll = array(), $siteIdsMainbase = array(), $curl, $search_result;
  public function __construct() {
    $this->config = json_decode(file_get_contents('../etc/config.json'));
  }

  public function run() {
    $ret = array();
    try {
      $this->collectSapeIds();
      $this->initCurl();
      $this->login();
      $this->checkIds();
      $this->parseResp();

      foreach ($this->siteIdsAll as $sapeId) {
        $isInMainbase = in_array($sapeId, $this->siteIdsMainbase);
        $ret['ids'][$sapeId] = (int)$isInMainbase;
      }
      $ret['success'] = 1;
    } catch (Exception $e) {
      $ret['error'] = $e->GetMessage();
      $ret['success'] = 0;
    }
    die(json_encode($ret));
  }

  private function collectSapeIds() {
    if(!isset($_REQUEST['ids']) || !is_array($_REQUEST['ids'])) throw new Exception("No sape sites", 1);
    $this->siteIdsAll = array_unique($_REQUEST['ids']);
    $this->siteIdsAll = array_map(function($val){
      $val = (int)$val;
      return $val > 0 ? $val : null;
    }, $this->siteIdsAll);
    $this->siteIdsAll = array_filter($this->siteIdsAll);
    if(count($this->siteIdsAll) == 0) throw new Exception("No sape sites", 1);
    if(count($this->siteIdsAll) > 200) throw new Exception("To much sape sites", 1);
  }

  private function initCurl() {
    $this->curl = curl_init();
    curl_setopt($this->curl, CURLOPT_COOKIEJAR, '');
    curl_setopt($this->curl, CURLOPT_COOKIEFILE, '');
    curl_setopt($this->curl, CURLOPT_USERAGENT, "Opera/10.00 (Windows NT 5.1; U; ru) Presto/2.2.0");
    curl_setopt($this->curl, CURLOPT_REFERER, 'https://www.sape.ru/orders.php');
    curl_setopt($this->curl, CURLOPT_TIMEOUT, 10);
    curl_setopt($this->curl, CURLOPT_POST, 1);
    curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
  }

  private function login() {
    $url = 'https://auth.sape.ru/login/';
    $post = 'r=&username='.urlencode($this->config->login).'&password='.urlencode($this->config->password).'&bindip=0&submit=%D0%92%D0%BE%D0%B9%D1%82%D0%B8';
    curl_setopt($this->curl, CURLOPT_URL, $url);
    curl_setopt($this->curl, CURLOPT_POSTFIELDS, $post);
    $auth_result = curl_exec($this->curl);
  }

  private function checkIds() {
    $url = 'https://www.sape.ru/ajax_orders.php?ajax_act=search&return_result=true';
    $post = 'act=s_order&filter_mode=0&project_id='.(int)$this->config->projectID.'&link_id='.(int)$this->config->linkID.'&show_mode=&pn=1&s_nogood=1&s_pr_from=&s_pr_2=&s_tr_1=&s_tr_2=&s_site_pr_1=&s_site_pr_2=&s_cy_from=&s_cy_2=&s_ext_links=&s_ext_links_forecast=&s_price_from=&s_price_2=&s_days_old_whois=&s_in_dmoz=2&s_in_yaca=2&s_in_dmoz_yaca=0&s_domain_level=&s_links_display_mode=-1&categories_selector=on&yaca_categories_selector=on&regions_selector=on&domain_zones_selector=on&s_words_type=0&s_words=&s_words_proximity=3&s_date_added=&s_site_id='
      .(implode(',', $this->siteIdsAll)).'&s_page_id=&s_no_double_in_project=&s_no_double_in_folder=&s_flag_blocked_in_yandex=0&s_flag_blocked_in_google=2&s_pages_per_site=preferred&ps=50&anchor_order=&order=&show_mode=1&name=&new_search=1';

    curl_setopt($this->curl, CURLOPT_URL, $url);
    curl_setopt($this->curl, CURLOPT_POSTFIELDS, $post);

    $this->search_result = curl_exec($this->curl);
  }

  private function parseResp() {
    if(preg_match_all('~<span\s+class="fs10">ID:\s+([0-9]+)\s+\|~', $this->search_result, $m)) {
      $this->siteIdsMainbase = $m[1];
    } else {
      throw new Exception("No sites founded, check 1) ids 2) login-password 3) regexp", 1);
    }
  }

}

$domainsInfo = new DomainsInfo();
$domainsInfo->run();
