<?php ?>

<a href="/create/" class="mb-3" style="width:1px;">
    <button class="btn btn-secondary" id="createBtn"<?php if (!isAdmin()){echo 'disabled';}?>>Створити</button>
</a>


<div class="container d-flex justify-content-end gap-2 pe-4">
    <input type="text" id="searchInput" placeholder="Пошук типів авто..."/>
    <button type="button" class="btn btn-secondary" id="searchBtn">Пошук</button>
    <button type="button" class="btn btn-light" disabled id="showAllBtn">Показати все</button>
</div>


<div class="vehicle-list mx-3" id="vehicleList">
    <div class="loading">Завантаження типів авто...</div>
</div>

