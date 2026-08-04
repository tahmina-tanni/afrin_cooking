
<?php
// utils/SortStrategy.php - Strategy Pattern (Afrin's Cooking)
// Recipe sort (latest vs popular vs A-Z) 

class SortStrategy {

    // Sort type 
    public static function getOrderClause($type) {
        switch ($type) {
            case 'popular':
                return " ORDER BY rating DESC, reviews DESC";

            case 'az':
                return " ORDER BY r.title ASC";

            case 'latest':
            default:
                return " ORDER BY r.created_at DESC";
        }
    }
}
?>
