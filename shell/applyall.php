<?php
require '../app/Mage.php';

Mage::app();
Mage::getModel('catalogrule/rule')->applyAll();
