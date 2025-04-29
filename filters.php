<?php
function renderFilters($filters) {
    foreach ($filters as $filterName => $options) {
        $filterKey = strtolower($filterName); // Used as data-key and class prefix
        echo "<div class='filter-section'>";

        echo '<button class="filter-toggle" onclick="toggleFilter(this)">';
        echo '<div class="filter-header">';
        echo '<span class="dot"></span>';
        echo "<span class='filter-title'>{$filterName}</span>";
        echo "<span class='arrow-icon'>
                <svg xmlns='http://www.w3.org/2000/svg' width='1em' height='1em' fill='none' viewBox='0 0 24 24' stroke='none' style='font-size: 20px;'>
                    <path stroke='#000' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m5 8.5 7 7 7-7'></path>
                </svg>
              </span>";
        echo '</div>';
        echo '</button>';

        echo "<div class='filter-options' data-key='{$filterKey}'>";
        foreach ($options as $option) {
            $value = strtolower(str_replace(' ', '-', $option));
            echo "<label><input type='checkbox' class='filter-$filterKey' value='$value'> $option</label>";
        }
        echo "</div></div>";
    }
}
?>
