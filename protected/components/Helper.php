<?php

class Helper {
    static function formatBalanceStatusChangedDt($balance_status_changed_dt) {
        return $balance_status_changed_dt!="" ? intval((time()-strtotime($balance_status_changed_dt))/3600/24):"";
    }
}