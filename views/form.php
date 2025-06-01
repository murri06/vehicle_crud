<?php ?>

    <div class="row mb-4">
        <div class="col">
            <a href="/">
                <button class="btn btn-outline-secondary">Назад</button>
            </a>
        </div>
    </div>
<?php if (!isAdmin()) {
    echo "<h3> Ви не маєте доступу до створення та редагування інформації </h3>";

} else { ?>

    <form id="vehicleForm">
        <?php if (isset($id)): ?>
            <input type="hidden" id="vehicleId" value="<?= $id ?>">
        <?php endif; ?>

        <div class="form-group">
            <label class="form-label" for="vehicletitle">Назва типу авто*</label>
            <input class="form-control" style="width:20% !important;" type="text" id="vehicletitle" name="title"
                   required
                   placeholder="до прикладу, SUV, Sedan, Truck">
        </div>

        <button type="submit" class="btn btn-secondary mt-3" id="submitBtn">
            Зберегти
        </button>
        <?php if (isset($id)): ?>
            <button class="btn btn-dark mt-3" onclick="vehicleApp.deleteVehicle(<?= $id ?>)" type="button">Видалити
            </button>
        <?php endif; ?>
    </form>
    <?php
    if (isset($id)):
        ?>
        <script>
            let id = <?php echo $id; ?>;
            document.addEventListener('DOMContentLoaded', async function () {
                let data = await new VehicleCRUD().viewVehicle(id);
                if (data.data) {
                    document.getElementById('vehicletitle').value = data.data.title;
                }
            });
        </script>
    <?php endif;
}
