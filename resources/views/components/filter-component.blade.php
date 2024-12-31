<!-- Botão de ativação -->
<button id="toggle-filter-btn" onclick="toggleFilterSidebar()">
    <i class="bi bi-filter"></i> Filters
</button>

<div id="filter-container" class="filter-container">
    <div class="filter-header">
        <p class="filter-title">Filters</p>
    </div>

    <!-- Lista de filtros -->
    <ul class="filter-list">
        <li class="filter-item">
            <button class="filter-toggle" onclick="toggleFilter('plan-options')">Plan <i class="bi bi-chevron-down"></i></button>
            <div id="plan-options" class="filter-options">
                <!-- Preenchido dinamicamente -->
            </div>
        </li>
        <li class="filter-item">
            <button class="filter-toggle" onclick="toggleFilter('age-options')">Age <i class="bi bi-chevron-down"></i></button>
            <div id="age-options" class="filter-options">
                <!-- Preenchido dinamicamente -->
            </div>
        </li>
        <li class="filter-item">
            <button class="filter-toggle" onclick="toggleFilter('tags-options')">Tags <i class="bi bi-chevron-down"></i></button>
            <div id="tags-options" class="filter-options">
                <!-- Preenchido dinamicamente -->
            </div>
        </li>
        <li class="filter-item">
            <button class="filter-toggle" onclick="toggleFilter('author-options')">Author <i class="bi bi-chevron-down"></i></button>
            <div id="author-options" class="filter-options">
                <!-- Preenchido dinamicamente -->
            </div>
        </li>
        <li class="filter-item">
            <button class="filter-toggle" onclick="toggleFilter('reading-time-options')">Reading Time <i class="bi bi-chevron-down"></i></button>
            <div id="reading-time-options" class="filter-options">
                <!-- Preenchido dinamicamente -->
            </div>
        </li>
    </ul>
</div>
