<?php
/**
 * Mzax Emarketing (www.mzax.de)
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this Extension in the file LICENSE.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Mzax
 * @package     Mzax_Emarketing
 * @author      Jacob Siefer (jacob@mzax.de)
 * @copyright   Copyright (c) 2015 Jacob Siefer
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Class Mzax_Emarketing_Emarketing_CampaignController
 */
class Mzax_Emarketing_Emarketing_CampaignController extends Mzax_Emarketing_Controller_Admin_Action
{
    /**
     * @var string[]
     */
    protected $_publicActions = array('filterPreview');

    /**
     * @return void
     */
    public function indexAction()
    {
        $session = $this->_getSession();

        $this->_title($this->__('eMarketing'))
             ->_title($this->__('Manage Campaigns'));

        if (!$this->_config->get('mzax_emarketing/general/enable')) {
            $msg = $this->__(
                'The emarketing extension is disabled, no cron jobs are triggered. <a href="%s">Change Settings</a>',
                $this->getUrl('*/system_config/edit', array('section' => 'mzax_emarketing'))
            );

            $session->addWarning($msg);
        }
        $this->loadLayout();
        $this->_setActiveMenu('promo/emarketing');

        $this->_addContent(
            $this->getLayout()->createBlock('mzax_emarketing/campaign_view', 'mzax_emarketing')
        );

        $this->renderLayout();
    }

    /**
     * Create new campaign action
     *
     * @return void
     */
    public function newAction()
    {
        $campaign = $this->_initCampaign();

        if ($values = $this->_getSession()->getCampaignData(true)) {
            if (isset($values['campaign'])) {
                $campaign->addData($values['campaign']);
            }
            if (isset($values['filters']) && is_array($values['filters'])) {
                $campaign->setFilters($values['filters']);
            }
        }

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('campaign');
            if (!empty($data)) {
                $campaign->addData($data);
            }
        }

        if ($campaign->hasData('medium')) {
            $this->_forward('edit');
            return;
        }

        $this->_title($this->__('eMarketing'))
             ->_title($this->__('New Campaign'));

        $this->loadLayout();
        $this->_setActiveMenu('promo/emarketing');
        $this->renderLayout();
    }

    /**
     * Create new campaign from preset action
     *
     * @return void
     */
    public function usePresetAction()
    {
        $presetName = $this->getRequest()->getParam('preset');

        if (!$presetName) {
            $this->_redirect('*/*/new');
            return;
        }

        try {
            /* @var $preset Mzax_Emarketing_Model_Campaign_Preset */
            $preset = Mage::getModel('mzax_emarketing/campaign_preset')->load($presetName);

            $campaign = $preset->makeCampaign();

            Mage::register('current_campaign', $campaign);

            $this->_forward('new');
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($this->__("Failed to load preset"));
            $this->_redirect('*/*/new');
            return;
        } catch (Exception $e) {
            if (Mage::getIsDeveloperMode()) {
                $this->_getSession()->addError($e->getMessage());
            } else {
                $this->_getSession()->addError($this->__("Failed to load preset"));
            }
            Mage::logException($e);
            $this->_redirect('*/*/new');
            return;
        }
    }

    /**
     * @return void
     */
    public function editAction()
    {
        $campaign = $this->_initCampaign();

        if ($values = $this->_getSession()->getCampaignData(true)) {
            if (isset($values['campaign'])) {
                $campaign->addData($values['campaign']);
            }
            if (isset($values['filters']) && is_array($values['filters'])) {
                $campaign->setFilters($values['filters']);
            }
        } elseif ($campaign->getId() && $this->_getSession()->getData('init_default_filters', true) == $campaign->getId()) {
            $campaign->getRecipientProvider()->setDefaultFilters();
        }

        $this->_title($this->__('eMarketing'))
             ->_title($this->__('Edit %s', $campaign->getName()));

        $this->loadLayout();
        $this->_setActiveMenu('promo/emarketing');
        $this->renderLayout();
    }

    /**
     * @return void
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $campaign = $this->_initCampaign('campaign_id');
            $filter = $this->getRequest()->getPost('filter', array());

            try {
                $redirectBack = $this->getRequest()->getParam('back', false);

                if (isset($data['campaign'])) {
                    if (isset($data['campaign']['medium_data'])) {
                        $mediumData = $data['campaign']['medium_data'];
                        unset($data['campaign']['medium_data']);
                        $campaign->getMediumData()->addData($mediumData);
                    }
                    $data['campaign'] = $this->_filterPostData($data['campaign']);
                    $campaign->addData($data['campaign']);
                }

                if (is_array($filter)) {
                    $provider = $campaign->getRecipientProvider();
                    if ($provider) {
                        $provider->getFilter()->loadFlatArray($filter);
                    }
                }

                $variations = $this->getRequest()->getPost('variation');
                if (is_array($variations)) {
                    foreach ($variations as $variationId => $variationData) {
                        $variation = $campaign->getVariation($variationId);
                        if ($variation) {
                            if (isset($variationData['medium_data'])) {
                                $mediumData = $variationData['medium_data'];
                                unset($variationData['medium_data']);
                                $variation->getMediumData()->addData($mediumData);
                            }
                            $variation->addData($variationData);
                        }
                    }
                }

                $initDefaultFilters = !$campaign->getId() && !$campaign->getPresetName();
                $campaign->save();

                if ($initDefaultFilters) {
                    $this->_getSession()->setData('init_default_filters', $campaign->getId());
                }


                Mage::app()->cleanCache(array($campaign::CACHE_TAG));

                if ($campaign->getPresetName()) {
                    $this->_getSession()->addSuccess($this->__('Campaign was successfully created from preset `%s`.', $campaign->getPresetName()));
                    $this->_getSession()->addNotice($this->__("Double check your filter and segmentation settings!"));
                } else {
                    $this->_getSession()->addSuccess($this->__('Campaign was successfully saved'));
                }
                Mage::dispatchEvent('adminhtml_campaign_save_after', array('campaign' => $campaign));

                if ($redirectBack) {
                    $this->_redirect('*/*/edit', array(
                        'id'    => $campaign->getId(),
                        '_current'=>true
                    ));
                    return;
                }
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_getSession()->setCampaignData($data);
                $this->getResponse()->setRedirect($this->getUrl('*/*/edit', array('id'=>$campaign->getId())));
                return;
            }
        }

        $this->getResponse()->setRedirect($this->getUrl('*/*'));
    }

    /**
     * Manually aggregate campaign
     *
     * @return void
     * @throws Exception
     */
    public function aggregateAction()
    {
        $campaign = $this->_initCampaign();
        if ($campaign->getId()) {
            try {
                $campaign->aggregate();
                $this->_getSession()->addSuccess($this->__('Campaign aggregated successfully'));
            } catch (Exception $e) {
                if (Mage::getIsDeveloperMode()) {
                    throw $e;
                }
                Mage::logException($e);
                $this->_getSession()->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/edit', array('_current' => true));
    }

    /**
     * Find new recipients that match the current filer
     * at this very moment in time and queue for the campaign
     * medium.
     *
     * @return void
     */
    public function queueAction()
    {
        $campaign = $this->_initCampaign();
        if ($campaign->getId()) {
            $count = $campaign->findRecipients(true);
            if ($count) {
                $this->_getSession()->addSuccess($this->__("%s recipients were found.", $count));
            } else {
                $this->_getSession()->addNotice($this->__("No new recipients found"));
            }
        }

        $this->_redirect('*/*/edit', array('_current' => true, 'tab' => 'tasks'));
    }

    /**
     * Start campaign
     *
     * @return void
     */
    public function startAction()
    {
        $campaign = $this->_initCampaign();
        if ($campaign->getId()) {
            $campaign->start()->save();
            $this->_getSession()->addSuccess($this->__("Campaign is now running."));
        }

        $this->_redirect('*/*/edit', array('_current' => true));
    }

    /**
     * Mass campaign start action
     *
     * @return void
     * @throws Exception
     */
    public function massStartAction()
    {
        $campaigns = $this->_initCampaigns();
        if (!empty($campaigns)) {
            $count = 0;
            /* @var $campaign Mzax_Emarketing_Model_Campaign */
            foreach ($campaigns as $campaign) {
                if ($campaign->isArchived()) {
                    $this->_getSession()->addWarning($this->__('Campaign `%s` is archived and could not get started.', $campaign->getName()));
                    continue;
                }
                if ($campaign->isRunning()) {
                    continue;
                }
                try {
                    $campaign->start()->save();
                    $count++;
                } catch (Exception $e) {
                    $this->_getSession()->addError($this->__("Failed to start campaign `%s`", $campaign->getName()));
                    if (Mage::getIsDeveloperMode()) {
                        throw $e;
                    }
                    Mage::logException($e);
                }
            }

            if ($count) {
                $this->_getSession()->addSuccess($this->__("%s campaign(s) have been started.", $count));
            }
        }

        $this->_redirect('*/*/index');
    }

    /**
     * Stop campaign immediately and remove all recipients
     * Useful as an emergency stop if something is wrong.
     *
     * @return void
     */
    public function stopAction()
    {
        $campaign = $this->_initCampaign();
        if ($campaign->getId()) {
            $campaign->stop()->save();
            $this->_getSession()->addNotice($this->__("Campaign has been stopped"));
        }

        $this->_redirect('*/*/edit', array('_current' => true));
    }

    /**
     * Mass campaign stop action
     *
     * @return void
     * @throws Exception
     */
    public function massStopAction()
    {
        $campaigns = $this->_initCampaigns();
        if (!empty($campaigns)) {
            $count = 0;
            /* @var $campaign Mzax_Emarketing_Model_Campaign */
            foreach ($campaigns as $campaign) {
                if (!$campaign->isRunning()) {
                    continue;
                }
                try {
                    $campaign->stop()->save();
                    $count++;
                } catch (Exception $e) {
                    $this->_getSession()->addError($this->__("Failed to stop campaign `%s`", $campaign->getName()));
                    if (Mage::getIsDeveloperMode()) {
                        throw $e;
                    }
                    Mage::logException($e);
                }
            }

            if ($count) {
                $this->_getSession()->addSuccess($this->__("%s campaign(s) have been stopped.", $count));
            }
        }

        $this->_redirect('*/*/index');
    }

    /**
     * Archive campaign
     *
     * @return void
     */
    public function archiveAction()
    {
        $campaign = $this->_initCampaign();
        if ($campaign->getId()) {
            if ($campaign->isRunning()) {
                $this->_getSession()->addWarning($this->__("You can not archive a running campaign."));
            } else {
                $campaign->isArchived(!$campaign->isArchived());
                $campaign->save();
                if ($campaign->isArchived()) {
                    $this->_getSession()->addSuccess($this->__("Campaign moved to archive."));
                } else {
                    $this->_getSession()->addSuccess($this->__("Campaign successfully unarchived."));
                }
            }
        }
        $this->_redirect('*/*/edit', array('_current' => true, 'tab' => 'tasks'));
    }

    /**
     * Mass campaign stop action
     *
     * @return void
     * @throws Exception
     */
    public function massArchiveAction()
    {
        $campaigns = $this->_initCampaigns();
        if (!empty($campaigns)) {
            $count = 0;
            /* @var $campaign Mzax_Emarketing_Model_Campaign */
            foreach ($campaigns as $campaign) {
                if ($campaign->isRunning()) {
                    $this->_getSession()->addWarning($this->__('Campaign `%s` is running and could not get archived.', $campaign->getName()));
                    continue;
                }
                if ($campaign->isArchived()) {
                    continue;
                }
                try {
                    $campaign->isArchived(true);
                    $campaign->save();
                    $count++;
                } catch (Exception $e) {
                    $this->_getSession()->addError($this->__("Failed to archive campaign `%s`", $campaign->getName()));
                    if (Mage::getIsDeveloperMode()) {
                        throw $e;
                    }
                    Mage::logException($e);
                }
            }

            if ($count) {
                $this->_getSession()->addSuccess($this->__("%s campaign(s) have been archived.", $count));
            }
        }

        $this->_redirect('*/*/index');
    }

    /**
     * Duplicate campaign action
     *
     * @return void
     */
    public function duplicateAction()
    {
        $campaign = $this->_initCampaign();
        if (!$campaign->getId()) {
            $this->_redirect('*/*/index');
        }

        $newCampaign = clone $campaign;
        $newCampaign->setName($this->__('Copy of %s', $campaign->getName()));
        $newCampaign->isArchived(false);
        $newCampaign->save();
        $this->_getSession()->addSuccess($this->__("Campaign successfully duplicated."));

        $this->_redirect('*/*/edit', array('_current' => true, 'tab' => 'tasks', 'id' => $newCampaign->getId()));
    }

    /**
     * Download campaign template
     *
     * @return void
     */
    public function downloadAction()
    {
        $campaign = $this->_initCampaign();
        if ($campaign->getId()) {
            try {
                $data = $campaign->export();

                $fileName = preg_replace('/[^a-z0-9]+/', '-', strtolower($campaign->getName()));
                $fileName.= Mzax_Emarketing_Model_Resource_Campaign_Preset::SUFFIX;

                $contentLength = strlen($data);

                $this->getResponse()
                    ->setHttpResponseCode(200)
                    ->setHeader('Pragma', 'public', true)
                    ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
                    ->setHeader('Content-type', 'text/plain', true)
                    ->setHeader('Content-Length', $contentLength)
                    ->setHeader('Content-Disposition', 'attachment; filename="'.$fileName.'"')
                    ->setHeader('Last-Modified', date('r'))
                    ->setBody($data);

                return;
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*');
    }

    /**
     * Install new preset from campaign
     *
     * @return void
     */
    public function installAsPresetAction()
    {
        $session = $this->_sessionManager->getAdminSession();
        $campaign = $this->_initCampaign();

        if ($campaign->getId()) {
            try {
                $data = $campaign->export();

                $filename = preg_replace('/[^a-z0-9]+/', '-', strtolower($campaign->getName()));

                /** @var Mage_Admin_Model_User $user */
                $user = $session->getData('user');

                /* @var $resource Mzax_Emarketing_Model_Resource_Campaign_Preset */
                $resource = Mage::getResourceSingleton('mzax_emarketing/campaign_preset');
                $resource->install('local-' . $filename, $data, true, $user->getName());

                $this->_getSession()->addSuccess('Preset installed successfully');
                $this->_redirect('*/*/new');
                return;
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*');
    }

    /**
     * Send the campaign message to all recipients
     *
     * @return void
     */
    public function prepareAction()
    {
        $campaign = $this->_initCampaign();
        if ($campaign->getId()) {
            $count = $campaign->sendRecipients(array('timeout' => 60));
            $this->_getSession()->addSuccess($this->__("%s emails have been prepared and moved to outbox.", $count));
        }

        $this->_redirect('*/*/edit', array('_current' => true, 'tab' => 'tasks'));
    }

    /**
     * @return void
     */
    public function addVariationAction()
    {
        $campaign = $this->_initCampaign();

        $variation = $campaign->createVariation();
        $variation->save();

        $this->_getSession()->addSuccess("New Variation '{$variation->getName()}' has been created.");

        $this->_redirect('*/*/edit', array('variation' => $variation->getId(), '_current' => true));
    }

    /**
     * Delete campaign content variation
     *
     * @return void
     */
    public function deleteVariationAction()
    {
        $campaign = $this->_initCampaign();

        $variationId = $this->getRequest()->getParam('variation');
        if ($variationId == 'all') {
            /* @var $variation Mzax_Emarketing_Model_Campaign_Variation */
            foreach ($campaign->getVariations() as $variation) {
                $variation->isRemoved(true);
            }
            $campaign->setDataChanges(true);
            $campaign->save();
        } elseif ($variationId) {
            $variation = $campaign->getVariation($variationId);
            if ($variation) {
                $variation->isRemoved(true);
                $variation->save();
                $this->_getSession()->addSuccess("Variation '{$variation->getName()}' has been removed.");
            }
        }
        $this->_redirect('*/*/edit', array('_current' => true));
    }

    /**
     * Queue list Ajax action
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody($this->getLayout()->createBlock('mzax_emarketing/campaign_grid')->toHtml());
    }

    /**
     * Recieve campaign recipients grid
     *
     * @return Mzax_Emarketing_Block_Campaign_Edit_Tab_Recipients_Grid
     */
    public function getRecipientsGridBlock()
    {
        $this->loadLayout();

        return $this->getLayout()->createBlock('mzax_emarketing/campaign_edit_tab_recipients_grid');
    }

    /**
     *
     */
    public function errorGridAction()
    {
        $this->_renderTab('errors');
    }

    /**
     * @return void
     */
    public function queryReportAction()
    {
        $this->_initCampaign();

        $jsonParams = $this->getRequest()->getRawBody();
        $params = Zend_Json::decode($jsonParams);

        /* @var $query Mzax_Emarketing_Model_Report_Query */
        $query = Mage::getModel('mzax_emarketing/report_query');
        $query->setParams($params);

        $table = $query->getDataTable();

        /* @var $report Mzax_Emarketing_Block_Campaign_Edit_Tab_Report */
        $report = $this->getLayout()->createBlock('mzax_emarketing/campaign_edit_tab_report');
        $report->prepareTable($table);

        $this->getResponse()
            ->setHeader('Content-Type', 'application/json')
            ->setBody($table->asJson());
    }

    /**
     * @return void
     */
    public function filterGridAction()
    {
        $filterId = $this->getRequest()->getParam('filter_id');

        $campaign = $this->getCurrentCampaign();
        $filter = $campaign->getRecipientProvider()->getFilterById($filterId);

        if ($filter) {
            $this->loadLayout();
            $this->getLayout()->createBlock('mzax_emarketing/campaign_test_emulate')->prepareEmulation($filter);
            $grid = $this->getLayout()->createBlock('mzax_emarketing/campaign_test')->getFilterGrid($filter);

            $this->getResponse()->setBody($grid->getHtml());
        } else {
            $this->getResponse()->setBody($this->__("Filter not found"));
        }
    }

    /**
     * @return void
     */
    public function filterPreviewAction()
    {
        $filterId = $this->getRequest()->getParam('filter_id');

        $campaign = $this->getCurrentCampaign();
        $filter = $campaign->getFilterById($filterId);

        $this->loadLayout('mzax_popup');
        if ($filter) {
            /** @var Mzax_Emarketing_Block_Filter_Test_Single $block */
            $block = $this->getLayout()->getBlock('filter_test');
            $block->setFilter($filter);
            $block->setDefaultLimit(20);
        }
        $this->renderLayout();
    }

    /**
     * Test Filter Tab Action
     *
     * @return void
     */
    public function testFiltersAction()
    {
        $this->getCurrentCampaign();

        $this->loadLayout('mzax_popup');

        if ($this->getRequest()->getParam('isAjax')) {
            $block = $this->getLayout()->getBlock('filter.test');
            $this->getResponse()->setBody($block->toHtml());
        } else {
            $this->renderLayout();
        }
    }

    /**
     * Recipients Tab Action
     *
     * @return void
     */
    public function recipientsAction()
    {
        $this->_renderTab();
    }

    /**
     * Recipients Tab Action
     *
     * @return void
     */
    public function recipientsGridAction()
    {
        $this->_renderTab('recipients_grid');
    }

    /**
     * Bounce Tab Action
     *
     * @return void
     */
    public function inboxAction()
    {
        $this->_renderTab();
    }

    /**
     * Report Tab Action
     *
     * @return void
     */
    public function reportAction()
    {
        $this->_renderTab();
    }

    /**
     * Renders a given tab
     *
     * @param string $tab
     */
    protected function _renderTab($tab = null)
    {
        if (!$tab) {
            $tab = $this->getRequest()->getActionName();
        }

        $this->_initCampaign();
        $block = 'mzax_emarketing/campaign_edit_tab_' . $tab;

        if ($this->getRequest()->getParam('isAjax')) {
            $this->loadLayout();
            $block = $this->getLayout()->createBlock($block);
            $this->getResponse()->setBody($block->toHtml());
        } else {
            $this->loadLayout(array('mzax_popup', 'mzax_emarketing_campaign_tab'));
            $block = $this->getLayout()->createBlock($block);
            $this->getLayout()->getBlock('content')->append($block);
            $this->renderLayout();
        }
    }

    /**
     * @return void
     */
    public function exportRecipientsAction()
    {
        $this->_initCampaign();

        $grid = $this->getRecipientsGridBlock();

        $fileName = 'campaign-data.csv';
        $content = $grid->getCsv();
        $contentLength = strlen($content);
        //$this->_prepareDownloadResponse('campaign-data.csv', $grid->getCsvFile());

        $this->getResponse()
            ->setHttpResponseCode(200)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
            ->setHeader('Content-type', 'text/csv', true)
            ->setHeader('Content-Length', $contentLength)
            ->setHeader('Content-Disposition', 'attachment; filename="'.$fileName.'"')
            ->setHeader('Last-Modified', date('r'))
            ->setBody($grid->getCsv());
    }

    /**
     * @return void
     */
    public function templateHtmlAction()
    {
        $response = new Varien_Object();
        $response->setError(false);

        $templateId = $this->getRequest()->getParam('template');
        /* @var $template Mzax_Emarketing_Model_Template */
        $template = Mage::getModel('mzax_emarketing/template')->load($templateId);

        $response->setHtml($template->getBody());

        $this->getResponse()
            ->setHeader('Content-Type', 'application/json')
            ->setBody($response->toJson());
    }

    /**
     * Allow quick saving email content
     *
     * @return void
     * @throws Mage_Exception
     */
    public function quicksaveAction()
    {
        $response = new Varien_Object();
        $response->setError(false);

        try {
            $campaign = $this->_initCampaign();
            $content = $campaign;

            $variationId = $this->getRequest()->getParam('variation');
            if ($variationId) {
                $content = $campaign->getVariation($variationId);
            }

            if ($content && $content->getId()) {
                $data = $this->getRequest()->getPost('data');
                if (!empty($data)) {
                    $content->getMediumData()->addData($data);
                    $content->save();
                }
            } else {
                throw new Mage_Exception("Campaign or variation not found");
            }

            Mage::app()->cleanCache(array($campaign::CACHE_TAG));
        } catch (Exception $e) {
            $response->setError(true);
            $response->setMessage($e->getMessage());
        }

        $this->getResponse()
            ->setHeader('Content-Type', 'application/json')
            ->setBody($response->toJson());
    }

    /**
     * Preview Newsletter template
     *
     * @return void
     */
    public function previewAction()
    {
        $campaign = $this->_initCampaign();

        $this->_title($this->__('eMarketing'))
             ->_title($this->__('Preview Email'));

        $this->loadLayout('mzax_popup');
        $this->_addContent(
            $this->getLayout()->createBlock('mzax_emarketing/campaign_preview')->setCampaign($campaign)
        );
        $this->renderLayout();
    }

    /**
     * @return void
     */
    public function updateLinksAction()
    {
        $request = $this->getRequest();

        if ($request->isPost() && $request->getPost('update_links')) {
            $optout = $request->getParam('optout');
            if (is_array($optout)) {
                Mage::getResourceSingleton('mzax_emarketing/link')->updateOptoutFlag($optout);
            }
        }

        $this->_redirect('*/*/preview', array('_current' => true));
    }

    /**
     * @return void
     */
    public function sendTestMailAction()
    {
        $campaign = $this->_initCampaign('id');

        $recipientId = $this->getRequest()->getParam('recipient');

        $recipient = $campaign->createMockRecipient($recipientId);
        $recipient->prepare();

        Mage::register('current_recipient', $recipient);

        $this->_title($this->__('eMarketing'))
             ->_title($this->__('Send Email'));

        $this->loadLayout('mzax_popup');
        $this->renderLayout();
    }

    /**
     * @return void
     * @throws Exception
     */
    public function sendTestMailPostAction()
    {
        try {
            $campaign = $this->_initCampaign('id');

            $objectId = $this->getRequest()->getParam('object_id');
            $recipient = $campaign->createMockRecipient($objectId);

            Mage::register('current_recipient', $recipient);
            $recipient->prepare();
            $recipient->setForceAddress($this->getRequest()->getParam('recipient_email'));
            $recipient->setAddress($this->getRequest()->getParam('recipient_email'));
            $recipient->setName($this->getRequest()->getParam('recipient_name'));

            if ($variationId = (int) $this->getRequest()->getPost('variation')) {
                $recipient->setVariationId($variationId);
            }

            $recipient->isPrepared(true);
            $recipient->save();

            $campaign->getMedium()->sendRecipient($recipient);

            $this->_getSession()->addSuccess($this->__("Test mail send to '%s'", $recipient->getAddress()));
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            if (Mage::getIsDeveloperMode()) {
                throw $e;
            }
        }
        $this->_redirect('*/*/sendTestMail', array('_current' => true, 'variation' => $variationId));
    }

    /**
     * @throws Exception
     */
    public function mailTesterAction()
    {
        try {
            $campaign = $this->_initCampaign('id');

            $variationId = (int) $this->getRequest()->getParam('variation');
            $recipientId = (int) $this->getRequest()->getParam('$recipientId');

            $recipient   = $campaign->createMockRecipient($recipientId);

            $hash = "mzax" . $campaign->getId()
                           . $campaign->getName()
                           . microtime()
                           . $recipientId
                           . session_id();

            $hash = Mage::helper('mzax_emarketing')->compressHash(md5($hash));

            $id = "mzax-{$hash}";
            $email = "{$id}@mail-tester.com";


            Mage::register('current_recipient', $recipient);
            $recipient->prepare();
            $recipient->setForceAddress($email);
            $recipient->setAddress($email);

            if ($variationId = (int) $this->getRequest()->getParam('variation')) {
                $recipient->setVariationId($variationId);
            }

            $recipient->isPrepared(true);
            $recipient->save();

            $campaign->getMedium()->sendRecipient($recipient);

            $this->_redirectUrl('http://www.mail-tester.com/' . $id);
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            if (Mage::getIsDeveloperMode()) {
                throw $e;
            }
            $this->_getSession()->addError($this->__("Failed to send test email"));
            $this->_redirect('*/*/sendTestMail', array('_current' => true));
        }
    }

    /**
     * @return void
     */
    public function validateTestMailAction()
    {
        $response = new Varien_Object();
        $response->setError(0);

        $this->getResponse()->setBody($response->toJson());
    }

    /**
     * Delete campaign action
     */
    public function deleteAction()
    {
        $campaign = $this->_initCampaign();
        if ($campaign->getId()) {
            try {
                $campaign->delete();
                $this->_getSession()->addSuccess(Mage::helper('mzax_emarketing')->__('Campaign was deleted'));
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*');
    }

    /**
     * @return void
     */
    public function newFilterHtmlAction()
    {
        $campaign = $this->_initCampaign('campaign');

        $type = $this->getRequest()->getPost('type');

        $filter = $campaign->getRecipientProvider()->createFilterFromTypePath($type);
        $filter->setId($this->getRequest()->getPost('id'));

        $this->getResponse()->setBody($filter->asHtml());
    }

    /**
     * @return void
     */
    public function loadTemplateAction()
    {
        $campaign = $this->_initCampaign('campaign_id');
        if ($data = $this->getRequest()->getPost()) {
            if (isset($data['campaign']['template_id'])) {
                $templateId = (int) $data['campaign']['template_id'];

                $template = Mage::getModel('core/email_template')->load($templateId);
                if ($template->getId()) {
                    $data['campaign']['email_subject'] = $template->getTemplateSubject();
                    $data['campaign']['email_text'] = $template->getTemplateText();
                } else {
                    $this->_getSession()->addError($this->__('Template not found'));
                }
            }
            $this->_getSession()->setCampaignData($data);
        }
        $this->getResponse()->setRedirect($this->getUrl('*/*/edit', array('id'=>$campaign->getId())));
    }

    /**
     * @return void
     */
    public function validateAction()
    {
        $response = new Varien_Object();
        $response->setError(0);

        $this->getResponse()->setBody($response->toJson());
    }

    /**
     * Mass campaign add tag action
     *
     * @return void
     * @throws Exception
     */
    public function massAddTagAction()
    {
        $campaigns = $this->_initCampaigns();
        $tags = $this->getRequest()->getPost('tags');

        if (!empty($campaigns)) {
            $count = 0;
            /* @var $campaign Mzax_Emarketing_Model_Campaign */
            foreach ($campaigns as $campaign) {
                try {
                    $campaign->addTags($tags)->save();
                    $count++;
                } catch (Exception $e) {
                    $this->_getSession()->addError($this->__("Failed to add tags to campaign `%s`", $campaign->getName()));
                    if (Mage::getIsDeveloperMode()) {
                        throw $e;
                    }
                    Mage::logException($e);
                }
            }

            if ($count) {
                $this->_getSession()->addSuccess($this->__("%s campaign(s) have been updated.", $count));
            }
        }

        $this->_redirect('*/*/index');
    }

    /**
     * Mass campaign remove tag action
     *
     * @return void
     * @throws Exception
     */
    public function massRemoveTagAction()
    {
        $campaigns = $this->_initCampaigns();
        $tags = $this->getRequest()->getPost('tags');

        if (!empty($campaigns)) {
            $count = 0;
            /* @var $campaign Mzax_Emarketing_Model_Campaign */
            foreach ($campaigns as $campaign) {
                try {
                    $campaign->removeTags($tags)->save();
                    $count++;
                } catch (Exception $e) {
                    $this->_getSession()->addError($this->__("Failed to add tags to campaign `%s`", $campaign->getName()));
                    if (Mage::getIsDeveloperMode()) {
                        throw $e;
                    }
                    Mage::logException($e);
                }
            }

            if ($count) {
                $this->_getSession()->addSuccess($this->__("%s campaign(s) have been updated.", $count));
            }
        }

        $this->_redirect('*/*/index');
    }

    /**
     * init campaign
     *
     * @param string $idFieldName
     *
     * @return Mzax_Emarketing_Model_Campaign
     */
    protected function _initCampaign($idFieldName = 'id')
    {
        $campaign = Mage::registry('current_campaign');
        if (!$campaign) {
            $campaign = Mage::getModel('mzax_emarketing/campaign');
            if ($campaignId = (int) $this->getRequest()->getParam($idFieldName)) {
                $campaign->load($campaignId);
            }

            Mage::register('current_campaign', $campaign);
        }
        return $campaign;
    }

    /**
     * Retrieve campaigns for mass actions
     *
     * @param string $idsFieldName
     * @return Mzax_Emarketing_Model_Resource_Campaign_Collection
     */
    protected function _initCampaigns($idsFieldName = 'campaigns')
    {
        $campaignIds = $this->getRequest()->getPost($idsFieldName);
        if (!empty($campaignIds)) {
            /* @var $collection Mzax_Emarketing_Model_Resource_Campaign_Collection */
            $collection = Mage::getResourceModel('mzax_emarketing/campaign_collection');
            $collection->addIdFilter($campaignIds);

            Mage::register('current_campaigns', $collection);

            return $collection;
        }
        return null;
    }

    /**
     * Retrieve current campaign with all post
     * or current session data applied
     *
     * @param string $idFieldName
     *
     * @return Mzax_Emarketing_Model_Campaign
     */
    public function getCurrentCampaign($idFieldName = 'id')
    {
        $campaign  = $this->_initCampaign($idFieldName);

        $cacheKey = 'mzax_campaign_test_' . $campaign->getId();

        // if we have post data, apply those changes first
        // and store them to the session
        if ($data = $this->getRequest()->getPost()) {
            if (isset($data['campaign']) && isset($data['filter'])) {
                $this->_getSession()->setData($cacheKey, $data);
            }
        }
        $data = $this->_getSession()->getData($cacheKey);

        if (is_array($data)) {
            if (isset($data['campaign'])) {
                $campaign->addData($data['campaign']);
            }

            if (isset($data['filter'])) {
                $provider = $campaign->getRecipientProvider();
                if ($provider && is_array($data['filter'])) {
                    $provider->loadFlatArray($data['filter']);
                }
            }
        }

        return $campaign;
    }

    /**
     * Filtering posted data. Converting localized data if needed
     *
     * @param array
     *
     * @return array
     */
    protected function _filterPostData($data)
    {
        $data = $this->_filterDates($data, array('start_at', 'end_at'));

        return $data;
    }

    /**
     * ACL check
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        $session = $this->_sessionManager->getAdminSession();

        return $session->isAllowed('promo/emarketing/campaigns');
    }
}
