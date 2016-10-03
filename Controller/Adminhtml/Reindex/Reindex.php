<?php

namespace DigitalPianism\Reindex\Controller\Adminhtml\Reindex;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

/**
 * Class Index
 * @package DigitalPianism\Reindex\Controller\Adminhtml\Reindex
 */
class Reindex extends Action
{
    /**
     * Check the permission to run it
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('DigitalPianism_Reindex::reindex');
    }

    /**
     *
     * @return void
     */
    public function execute()
    {
        $indexerIds = $this->getRequest()->getParam('indexer_ids');
        if (!is_array($indexerIds)) {
            $this->messageManager->addError(__('Please select indexers.'));
        } else {
            try {
                foreach ($indexerIds as $indexerId) {
                    /** @var \Magento\Framework\Indexer\IndexerInterface $model */
                    $model = $this->_objectManager->get('Magento\Framework\Indexer\IndexerRegistry')->get($indexerId);
                    $model->reindexAll();
                }
                $this->messageManager->addSuccess(
                    __('%1 indexer(s) were reindexed.', count($indexerIds))
                );
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException(
                    $e,
                    __("We couldn't reindex indexer(s)' because of an error.")
                );
            }
        }
        $this->_redirect('indexer/indexer/list');
    }
}