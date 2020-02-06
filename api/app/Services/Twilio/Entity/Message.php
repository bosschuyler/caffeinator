<?php

namespace App\Services\Twilio\Entity;

use Twilio\Rest\Api\V2010\Account\MessageInstance;

class Message {
    protected $data = null;

    public function __construct(MessageInstance $message) {
        $this->data = $message;
    }

    public function getParam($key) {
        return $value = $this->data->$key;
    }

    public function getId() {
        return $this->getParam('sid');
    }

    const PERMISSION_ADMIN = 'admin';

    protected $attachments = null;
    
    public function getRecipients() {
        $recipients = collect([]);
        $recipients->push($this->data->to);
        return $recipients;
    }

    public function getSender() {
        return $this->data->from;
    }

    public function getType() {
        return 'SMS';
    }

    public function isSms() {
        return $this->getType() == 'SMS';
    }

    public function getService() {
        return 'twilio';
    }
    
    public function getStatus() {
        return ucfirst($this->getParam('status'));
    }

    public function isDelivered() {
        return $this->getStatus() == 'Delivered';
    }

    public function getMessage() {
        return $this->getParam('body');
    }

    public function isAdmin() {
        return $this->hasPermission(static::PERMISSION_ADMIN);
    }

    public function getDirection() {
        return $this->data->direction;
    }

    public function isInbound() {
        return $this->getDirection() == 'Inbound';
    }

    public function isOutbound() {
        return $this->getDirection() == 'Outbound';
    }

    public function getReadStatus() {
        return $this->data->status;
    }

    public function isRead() {
        return $this->getReadStatus() == 'Read';
    }

    public function isUnread() {
        return $this->getReadStatus() == 'Unread';
    }

    public function getCreatedAt() {
        $datetime = $this->data->dateCreated;
        $datetime->setTimeZone(new \DateTimeZone(date_default_timezone_get()));
        return $datetime;
    }

    public function getSentAt() {
        $datetime = $this->data->dateSent;
        $datetime->setTimeZone(new \DateTimeZone(date_default_timezone_get()));
        return $datetime;
    }

    public function hasAudioAttachments() {
        $attachments = $this->getAttachments();

        foreach($attachments as $attachment) {
            if($attachment->isAudioFile())
                return true;
        }

        return false;
    }

    public function getAttachments() {
        return $this->attachments;
    }
}