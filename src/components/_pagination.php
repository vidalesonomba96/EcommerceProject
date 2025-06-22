<?php
// File: EcommerceProject/src/components/_pagination.php

if (!function_exists('generate_pagination')) {
    // The function now accepts extra parameters to add to the URL
    function generate_pagination($current_page, $total_pages, $base_url = 'products.php', $extra_params = []) {
        if ($total_pages <= 1) {
            return;
        }

        // Build a query string from the extra parameters (e.g., &filter=deals)
        $query_string = '';
        if (!empty($extra_params)) {
            // Remove 'page' from params to avoid duplication, then build the query string
            unset($extra_params['page']);
            $query_string = '&' . http_build_query($extra_params);
        }

        echo '<nav aria-label="Page navigation" class="pagination-container">';
        echo '<ul class="pagination">';

        // "Previous" button
        if ($current_page > 1) {
            echo '<li class="page-item"><a class="page-link" href="' . $base_url . '?page=' . ($current_page - 1) . $query_string . '">&laquo; Previous</a></li>';
        } else {
            echo '<li class="page-item disabled"><span class="page-link">&laquo; Previous</span></li>';
        }

        // Page number links
        for ($i = 1; $i <= $total_pages; $i++) {
            if ($i == $current_page) {
                echo '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
            } else {
                echo '<li class="page-item"><a class="page-link" href="' . $base_url . '?page=' . $i . $query_string . '">' . $i . '</a></li>';
            }
        }

        // "Next" button
        if ($current_page < $total_pages) {
            echo '<li class="page-item"><a class="page-link" href="' . $base_url . '?page=' . ($current_page + 1) . $query_string . '">Next &raquo;</a></li>';
        } else {
            echo '<li class="page-item disabled"><span class="page-link">Next &raquo;</span></li>';
        }

        echo '</ul>';
        echo '</nav>';
    }
}
?>