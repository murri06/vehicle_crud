class VehicleCRUD {
    constructor() {
        this.apiUrls = {
            read: 'http://localhost:8080/api/read',
            readOne: (id) => `http://localhost:8080/api/vehicle/${id}`,
            create: 'http://localhost:8080/api/create',
            update: 'http://localhost:8080/api/update',
            delete: (id) => `http://localhost:8080/api/delete/${id}`,
            search: (keywords) => `http://localhost:8080/api/search/${encodeURIComponent(keywords)}`
        };

        this.initializeElements();
        this.attachEventListeners();

        if (this.vehicleList) {
            this.loadVehicleTypes();
        }
    }

    initializeElements() {
        this.form = document.getElementById('vehicleForm');
        this.titleInput = document.getElementById('vehicletitle');
        this.hiddenId = document.getElementById('vehicleId');
        this.submitBtn = document.getElementById('submitBtn');
        this.searchInput = document.getElementById('searchInput');
        this.searchBtn = document.getElementById('searchBtn');
        this.showAllBtn = document.getElementById('showAllBtn');
        this.vehicleList = document.getElementById('vehicleList');
        this.messageContainer = document.getElementById('messageContainer');
    }

    attachEventListeners() {

        if (this.form) {
            this.form.addEventListener('submit', (e) => this.handleSubmit(e));
        }

        if (this.searchBtn) {
            this.searchBtn.addEventListener('click', () => this.searchVehicles());
        }

        if (this.showAllBtn) {
            this.showAllBtn.addEventListener('click', () => this.loadVehicleTypes());
        }
        if (this.searchInput) {
            this.searchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') this.searchVehicles();
            });
        }
    }

    async makeRequest(url, options = {}) {
        try {
            const response = await fetch(url, {
                headers: {
                    'Content-Type': 'application/json',
                    ...options.headers
                },
                ...options
            });

            const data = await response.json();
            return {success: response.ok, data, status: response.status};
        } catch (error) {
            console.error('Request failed:', error);
            return {success: false, error: error.message};
        }
    }

    showMessage(message, type = 'info') {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${type}`;
        messageDiv.textContent = message;

        this.messageContainer.innerHTML = '';
        this.messageContainer.appendChild(messageDiv);

        setTimeout(() => {
            if (messageDiv.parentNode) {
                messageDiv.parentNode.removeChild(messageDiv);
            }
        }, 4000);
    }

    async loadVehicleTypes() {
        this.vehicleList.innerHTML = '<div class="loading">Завантаження типів авто...</div>';

        const result = await this.makeRequest(this.apiUrls.read);

        if (result.success && result.data.records) {
            this.displayVehicleTypes(result.data.records);
            this.showAllBtn.classList.add("btn-light");
            this.showAllBtn.classList.remove("btn-dark");
            this.showAllBtn.setAttribute("disabled", 1);
        } else {
            this.vehicleList.innerHTML = `
                <div class="empty-state">
                    <h3>Не знайдено записів</h3>
                    <p>Розпочніть роботу з додавання типів авто.</p>
                </div>
            `;
        }
    }

    displayVehicleTypes(vehicles) {
        if (vehicles.length === 0) {
            this.vehicleList.innerHTML = `
                <div class="empty-state">
                    <h3>Не знайдено записів</h3>
                    <p>Розпочніть роботу з додавання типів авто.</p>
                </div>
            `;
            return;
        }

        this.vehicleList.innerHTML = `<table class="table"> 
                                          <thead>
                                            <tr>
                                              <th scope="col">#</th>
                                              <th scope="col">Тип авто</th>
                                              <th scope="col">Дії</th>
                                            </tr>
                                          </thead><tbody>`
            + vehicles.map(vehicle => `
            <tr>
                <th class="col-2">${vehicle.id}</th>
                <td class="col-3">${this.escapeHtml(vehicle.title)}
                </td>
                <td class="col-3">
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        . . .
                        </button>
                        <ul class="dropdown-menu">
                            <li><a href="/vehicle/${vehicle.id}"><button class="dropdown-item btn btn-secondary">Перегляд</button></a></li>
                            <li><a href="/update/${vehicle.id}"><button class="dropdown-item btn btn-secondary edit-btn">Редагувати</button></a></li>
                            <li><button class="dropdown-item btn btn-secondary edit-btn" onclick="vehicleApp.deleteVehicle(${vehicle.id})">Видалити</button></li>
                        </ul>
                    </div>
                </td>
            </tr>
        `).join('') + `</tbody></table>`;
    }

    async handleSubmit(e) {
        e.preventDefault();

        const title = this.titleInput.value.trim();
        const id = this.hiddenId;

        if (!title) {
            this.showMessage('Назва типу авто обов\'язкова', 'error');
            return;
        }

        const vehicleData = {title};

        if (id) {
            vehicleData.id = id.value;
            await this.updateVehicle(vehicleData);
        } else {
            await this.createVehicle(vehicleData);
        }
    }

    async createVehicle(vehicleData) {
        this.submitBtn.textContent = 'Створення...';
        this.submitBtn.disabled = true;

        const result = await this.makeRequest(this.apiUrls.create, {
            method: 'POST',
            body: JSON.stringify(vehicleData)
        });

        if (result.success) {
            this.showMessage('Тип авто був створений успішно!', 'success');
            this.form.reset();
            this.loadVehicleTypes();
        } else {
            this.showMessage(result.data?.message || 'Не вдалось створити тип авто', 'error');
        }

        this.submitBtn.textContent = 'Додати новий тип авто';
        this.submitBtn.disabled = false;
    }

    async viewVehicle(vehicleData) {
        return await this.makeRequest(this.apiUrls.readOne(vehicleData));
    }

    async updateVehicle(vehicleData) {
        this.submitBtn.textContent = 'Оновлення...';
        this.submitBtn.disabled = true;

        const result = await this.makeRequest(this.apiUrls.update, {
            method: 'PUT',
            body: JSON.stringify(vehicleData)
        });

        if (result.success) {
            this.showMessage('Інформацію оновлено успішно!', 'success');
        } else {
            this.showMessage(result.data?.message || 'Не вдалось оновити тип авто.', 'error');
        }

        this.submitBtn.textContent = 'Зберегти';
        this.submitBtn.disabled = false;
    }

    async deleteVehicle(id) {
        if (!confirm('Ви впевнені, що бажаєте видалити даний тип авто?')) {
            return;
        }

        const result = await this.makeRequest(this.apiUrls.delete(id), {
            method: 'DELETE'
        });

        if (result.success) {
            this.showMessage('Тип авто був успішно видалений!', 'success');

            if (id) {
                window.location.href="/";
            }
        } else {
            this.showMessage(result.data?.message || 'Не вдалось видалити тип авто.', 'error');
        }
    }

    async searchVehicles() {
        const keywords = this.searchInput.value.trim();

        if (!keywords) {
            return;
        }
        this.showAllBtn.classList.remove("btn-light");
        this.showAllBtn.classList.add("btn-dark");
        this.showAllBtn.removeAttribute("disabled");
        this.vehicleList.innerHTML = '<div class="loading">Пошук...</div>';

        const result = await this.makeRequest(this.apiUrls.search(keywords));

        if (result.success && result.data.records) {
            this.displayVehicleTypes(result.data.records);
        } else {
            this.vehicleList.innerHTML = `
                <div class="empty-state">
                    <h3>Записи не знайдено</h3>
                </div>
            `;
        }
    }

    escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, (m) => map[m]);
    }
}

let vehicleApp;
document.addEventListener('DOMContentLoaded', () => {
    vehicleApp = new VehicleCRUD();
});
