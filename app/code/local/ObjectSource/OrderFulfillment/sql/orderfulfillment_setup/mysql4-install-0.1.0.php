<?php
$installer = new Mage_Sales_Model_Resource_Setup('core_setup');
$installer->startSetup();

// Inject admin roles if they dont exist
//$resource = array("__root__,admin/page_cache,admin/dashboard,admin/sales,admin/sales/order,admin/sales/order/actions,admin/sales/order/actions/hold,admin/sales/order/actions/creditmemo,admin/sales/order/actions/unhold,admin/sales/order/actions/ship,admin/sales/order/actions/emails,admin/sales/order/actions/comment,admin/sales/order/actions/invoice,admin/sales/order/actions/capture,admin/sales/order/actions/email,admin/sales/order/actions/view,admin/sales/order/actions/reorder,admin/sales/order/actions/edit,admin/sales/order/actions/review_payment,admin/sales/order/actions/cancel,admin/sales/order/actions/create,admin/sales/amorderstatus,admin/sales/invoice,admin/sales/shipment,admin/sales/creditmemo,admin/sales/checkoutagreement,admin/sales/transactions,admin/sales/transactions/fetch,admin/sales/recurring_profile,admin/sales/billing_agreement,admin/sales/billing_agreement/actions,admin/sales/billing_agreement/actions/view,admin/sales/billing_agreement/actions/manage,admin/sales/billing_agreement/actions/use,admin/sales/tax,admin/sales/tax/classes_customer,admin/sales/tax/classes_product,admin/sales/tax/import_export,admin/sales/tax/rates,admin/sales/tax/rules,admin/EM_Colorswatches,admin/webforms,admin/webforms/webform_1,admin/webforms/settings,admin/webforms/quickresponses,admin/webforms/forms,admin/catalog,admin/catalog/reviews_ratings,admin/catalog/reviews_ratings/ratings,admin/catalog/reviews_ratings/reviews,admin/catalog/reviews_ratings/reviews/pending,admin/catalog/reviews_ratings/reviews/all,admin/catalog/tag,admin/catalog/tag/pending,admin/catalog/tag/all,admin/catalog/sitemap,admin/catalog/amshopby,admin/catalog/amshopby/settings,admin/catalog/amshopby/pages,admin/catalog/amshopby/ranges,admin/catalog/amshopby/filters,admin/catalog/search,admin/catalog/urlrewrite,admin/catalog/categories,admin/catalog/products,admin/catalog/update_attributes,admin/catalog/attributes,admin/catalog/attributes/sets,admin/catalog/attributes/attributes,admin/customer,admin/customer/manage,admin/customer/group,admin/customer/online,admin/promo,admin/promo/quote,admin/promo/catalog,admin/newsletter,admin/newsletter/magemonkey,admin/newsletter/template,admin/newsletter/subscriber,admin/newsletter/queue,admin/newsletter/problem,admin/cms,admin/cms/page,admin/cms/page/save,admin/cms/page/delete,admin/cms/block,admin/cms/widget_instance,admin/cms/media_gallery,admin/cms/poll,admin/dropcommon,admin/dropcommon/shipmethods,admin/dropcommon/warehouses,admin/report,admin/report/tags,admin/report/tags/product,admin/report/tags/popular,admin/report/tags/customer,admin/report/search,admin/report/statistics,admin/report/review,admin/report/review/product,admin/report/review/customer,admin/report/customers,admin/report/customers/orders,admin/report/customers/totals,admin/report/customers/accounts,admin/report/shopcart,admin/report/shopcart/abandoned,admin/report/shopcart/product,admin/report/products,admin/report/products/downloads,admin/report/products/lowstock,admin/report/products/viewed,admin/report/products/sold,admin/report/products/bestsellers,admin/report/salesroot,admin/report/salesroot/refunded,admin/report/salesroot/coupons,admin/report/salesroot/invoiced,admin/report/salesroot/shipping,admin/report/salesroot/sales,admin/report/salesroot/tax,admin/report/salesroot/paypal_settlement_reports,admin/report/salesroot/paypal_settlement_reports/fetch,admin/report/salesroot/paypal_settlement_reports/view,admin/system,admin/system/convert,admin/system/convert/gui,admin/system/convert/profiles,admin/system/convert/import,admin/system/convert/export,admin/system/index,admin/system/acl,admin/system/acl/roles,admin/system/acl/users,admin/system/cache,admin/system/extensions,admin/system/extensions/local,admin/system/extensions/custom,admin/system/api,admin/system/api/users,admin/system/api/consumer,admin/system/api/consumer/edit,admin/system/api/consumer/delete,admin/system/api/roles,admin/system/api/rest_roles,admin/system/api/rest_roles/add,admin/system/api/rest_roles/edit,admin/system/api/rest_roles/delete,admin/system/api/authorizedTokens,admin/system/api/rest_attributes,admin/system/api/rest_attributes/edit,admin/system/api/oauth_admin_token,admin/system/store,admin/system/adminnotification,admin/system/adminnotification/show_toolbar,admin/system/adminnotification/show_list,admin/system/adminnotification/mark_as_read,admin/system/adminnotification/remove,admin/system/order_statuses,admin/system/config,admin/system/config/persistent,admin/system/config/ambase,admin/system/config/amstore,admin/system/config/amconf,admin/system/config/downloadable,admin/system/config/newsletter,admin/system/config/wishlist,admin/system/config/contacts,admin/system/config/sitemap,admin/system/config/api,admin/system/config/amorderstatus,admin/system/config/amshopby,admin/system/config/colorswatches,admin/system/config/moneybookers,admin/system/config/webforms,admin/system/config/webformscrf,admin/system/config/wsalogmenu,admin/system/config/mandrill,admin/system/config/ebizmarts_abandonedcart,admin/system/config/ebizmarts_emails,admin/system/config/ebizmarts_autoresponder,admin/system/config/monkey,admin/system/config/reports,admin/system/config/google,admin/system/config/paypal,admin/system/config/cataloginventory,admin/system/config/payment,admin/system/config/catalog,admin/system/config/cms,admin/system/config/shipping,admin/system/config/payment_services,admin/system/config/promo,admin/system/config/carriers,admin/system/config/oauth,admin/system/config/general,admin/system/config/web,admin/system/config/design,admin/system/config/customer,admin/system/config/tax,admin/system/config/sales,admin/system/config/sales_email,admin/system/config/sales_pdf,admin/system/config/checkout,admin/system/config/system,admin/system/config/advanced,admin/system/config/trans_email,admin/system/config/admin,admin/system/config/dev,admin/system/config/currency,admin/system/config/rss,admin/system/config/sendfriend,admin/system/design,admin/system/currency,admin/system/currency/rates,admin/system/currency/symbols,admin/system/email_template,admin/system/variable,admin/system/myaccount,admin/system/tools,admin/system/tools/compiler,admin/system/tools/backup,admin/system/tools/backup/rollback,admin/global_search,admin/xmlconnect,admin/xmlconnect/mobile,admin/xmlconnect/history,admin/xmlconnect/templates,admin/xmlconnect/queue,admin/xmlconnect/admin_connect");

$adminRoles = array(array('role_name' => 'Malaysia', 'resource' => array('all')),
    array('role_name' => 'London', 'resource' => array('all')));

foreach ($adminRoles as $adminRole)
{
    $roles = Mage::getModel('admin/roles')->getCollection()
        ->addFieldToFilter('role_name', array('eq'=> $adminRole['role_name']));
    if (count($roles) > 0) {
        continue;
    }

    $role = Mage::getModel('admin/roles');
    $role->setName($adminRole['role_name'])
    //->setPid($this->getRequest()->getParam('parent_id', false))
        ->setRoleType('G');
    Mage::dispatchEvent(
        'admin_permissions_role_prepare_save',
        array('object' => $role, 'request' => null)
    );
    $role->save();

    Mage::getModel("admin/rules")
        ->setRoleId($role->getId())
        ->setResources($adminRole['resource'])
        ->saveRel();
}

$installer->addAttribute('catalog_product', "supplier", array(
    'group'      => 'General',
    'type'       => 'int',
    'input'      => 'select',
    'label'      => 'Supplier',
    'sort_order' => 1000,
    'required'   => false,
    'global'     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'backend'    => 'eav/entity_attribute_backend_array',
    'option'     => array (
        'values' => array (
            0 => 'Malaysia',
            1 => 'London',
        )
    ),

));

$entities = array(
    'quote_item',
    'order_item',
    'order'
);
$options = array(
    'type'     => Varien_Db_Ddl_Table::TYPE_TEXT,
    'visible'  => true,
    'required' => false
);

foreach ($entities as $entity) {
    $installer->addAttribute($entity, 'fulfillment_data', $options);
}

$this->run("
CREATE TABLE `{$this->getTable('orderfulfillment/productiondates')}` (
  `productiondate_id` mediumint(8) unsigned NOT NULL auto_increment,
  `label` varchar(255) NOT NULL,
  `processing_total_from` decimal(12,4) unsigned NOT NULL,
  `processing_total_to` decimal(12,4) unsigned NOT NULL,
  `delayed` int(8) unsigned NOT NULL,
  PRIMARY KEY  (`productiondate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->addAttribute('catalog_product', 'last_popular_at', array(
    'group'         => 'General',
    'type'          => 'datetime',
    'input'         => 'date',
    'label'         => 'Last Popular at',
    'backend'       => "eav/entity_attribute_backend_datetime",
    'visible'       => 0,
    'required'      => 0,
    'user_defined' => 1,
    'searchable' => 1,
    'filterable' => 0,
    'visible_on_front' => 0,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));

$installer->endSetup();