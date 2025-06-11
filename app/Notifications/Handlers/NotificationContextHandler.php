<?php

namespace App\Notifications\Handlers;

use Exception;

class NotificationContextHandler
{
    public static function getContext($notificationType)
    {
        switch ($notificationType) {
            case 'missing_items_notification':
                return MissingItemsNotificationHandler::getContext();
            case 'reorder_items_notification':
                return ReorderItemsNotificationHandler::getContext();
            case 'over_stocked_items_notification':
                return OverStockedItemsNotificationHandler::getContext();
            default:
                throw new Exception("Unsupported notification type: $notificationType");
        }
    }
}
