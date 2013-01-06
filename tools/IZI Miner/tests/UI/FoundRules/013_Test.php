<?php

require_once 'Bootstrap.php';

/**
 *  Stop mining when active rule changes
 */

class UI_FoundRules_013_Test extends IZI\Selenium\UITestCase
{
  public function testMyTestCase()
  {
    $this->open("?id_dm=TEST&sleep=1");
    $this->dragAndDropToObject("css=#attribute-nav-district", "css=#antecedent .cedent");
    $this->click("css=input[type=\"submit\"]");
    $this->click("id=add-im");
    $this->click("css=input[type=\"submit\"]");
    $this->dragAndDropToObject("css=#attribute-nav-quality", "css=#succedent .cedent");
    $this->click("css=input[type=\"submit\"]");
    $this->click("css=#mine-rules-confirm");
    $this->dragAndDropToObject("css=#attribute-nav-sex", "css=#antecedent .cedent");
    $this->click("css=input[type=\"submit\"]");
    for ($second = 0; ; $second++) {
        if ($second >= 60) $this->fail("timeout");
        try {
            if ($this->isElementPresent("css=.mining-stopped")) break;
        } catch (Exception $e) {}
        sleep(1);
    }

  }
}