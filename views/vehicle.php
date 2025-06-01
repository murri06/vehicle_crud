<?php ?>

<div class="row mb-4">
    <div class="col">
        <a href="/">
            <button class="btn btn-outline-secondary">Назад</button>
        </a>
    </div>
</div>

<!-- Loading State -->
<div id="loadingCard" class="card">
    <div class="card-body text-center py-5">
        <div class="spinner-border loading-spinner text-primary" role="status">
            <span class="visually-hidden">Завантаження...</span>
        </div>
        <p class="mt-3 mb-0 text-muted">Завантаження деталей...</p>
    </div>
</div>

<!-- Vehicle Details -->
<div id="vehicleContent" class="d-none">
    <!-- Main Vehicle Card -->
    <div class="card mb-4">
        <div class="row g-0">
            <div class="col-md-8">
                <div class="card-body h-100 d-flex flex-column">
                    <div class="mb-auto">
                        <h1 class="card-title mb-2" id="vehicleTitle">Деталі типу авто</h1>
                        <p class="card-text text-muted mb-3" id="vehicleSubtitle"></p>

                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="detail-label">Порядковий номер(ID)</div>
                                <div class="detail-value" id="vehicleID">-</div>
                            </div>
                            <div class="col-sm-6">
                                <div class="detail-label">Назва</div>
                                <div class="detail-value" id="vehicleName">-</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<?php if (isAdmin()): ?>
    <div class="card mt-4 ms-3">
        <div class="card-body text-center">
            <div class="d-flex flex-wrap justify-content-start gap-2">
                <a href="/update/<?= $id ?>">
                    <button class="btn btn-secondary">Редагувати</button>
                </a>
                <button class="btn btn-dark" onclick="vehicleApp.deleteVehicle(<?= $id ?>)">Видалити</button>
            </div>
        </div>
    </div>
<?php endif; ?>
<script>

    const vehicleId = <?= $id; ?>;

    // Load vehicle details when page loads
    document.addEventListener('DOMContentLoaded', function () {
        loadVehicleDetails();
    });

    async function loadVehicleDetails() {
        document.getElementById('vehicleContent').classList.remove('d-none');
        document.getElementById('loadingCard').classList.add('d-none');
        let data = await new VehicleCRUD().viewVehicle(vehicleId);
        if (data.data) {
            document.getElementById('vehicleID').textContent = data.data.id;
            document.getElementById('vehicleName').textContent = data.data.title;
        }
    }
</script>