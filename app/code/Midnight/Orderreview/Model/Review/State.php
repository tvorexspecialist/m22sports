<?php

namespace Midnight\Orderreview\Model\Review;

class State extends \Magento\Framework\Model\AbstractModel
{
    const READY_FOR_REVIEW  = 'ready_for_review';
    const READY_FOR_WINLINE = 'ready_for_winline';
    const DISMISSED = 'dismissed';
}
