// Admin Dashboard JavaScript

document.addEventListener('DOMContentLoaded', function() {
  // Elementos del DOM
  const tabBtns = document.querySelectorAll('.tab-btn');
  const tabContents = document.querySelectorAll('.tab-content');
  
  // Cargar estadísticas al inicio
  loadStatistics();
  
  // Cargar usuarios por defecto
  loadUsers();
  
  // Tabs
  tabBtns.forEach(btn => {
    btn.addEventListener('click', function() {
      const tabName = this.dataset.tab;
      
      // Actualizar botones
      tabBtns.forEach(b => b.classList.remove('active'));
      this.classList.add('active');
      
      // Actualizar contenido
      tabContents.forEach(c => c.classList.remove('active'));
      document.getElementById(`${tabName}-tab`).classList.add('active');
      
      // Cargar datos según el tab
      if (tabName === 'users' && !document.getElementById('usersContainer').dataset.loaded) {
        loadUsers();
      } else if (tabName === 'reviews' && !document.getElementById('reviewsContainer').dataset.loaded) {
        loadReviews();
      } else if (tabName === 'photos' && !document.getElementById('photosContainer').dataset.loaded) {
        loadPhotos();
      }
    });
  });
  
  // Búsqueda en usuarios
  document.getElementById('searchUsers')?.addEventListener('input', function(e) {
    const query = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('.user-row');
    
    rows.forEach(row => {
      const username = row.querySelector('.user-info h4').textContent.toLowerCase();
      const email = row.querySelector('.user-info p').textContent.toLowerCase();
      
      if (username.includes(query) || email.includes(query)) {
        row.style.display = 'grid';
      } else {
        row.style.display = 'none';
      }
    });
  });
  
  // Búsqueda en reseñas
  document.getElementById('searchReviews')?.addEventListener('input', function(e) {
    const query = e.target.value.toLowerCase();
    const cards = document.querySelectorAll('.review-card');
    
    cards.forEach(card => {
      const username = card.querySelector('.review-card__username').textContent.toLowerCase();
      const title = card.querySelector('.review-card__title').textContent.toLowerCase();
      const country = card.querySelector('.review-card__country').textContent.toLowerCase();
      
      if (username.includes(query) || title.includes(query) || country.includes(query)) {
        card.style.display = 'block';
      } else {
        card.style.display = 'none';
      }
    });
  });
  
  // Búsqueda en fotos
  document.getElementById('searchPhotos')?.addEventListener('input', function(e) {
    const query = e.target.value.toLowerCase();
    const cards = document.querySelectorAll('.photo-card');
    
    cards.forEach(card => {
      const username = card.querySelector('.photo-card__user').textContent.toLowerCase();
      const country = card.querySelector('.photo-card__country')?.textContent.toLowerCase() || '';
      
      if (username.includes(query) || country.includes(query)) {
        card.style.display = 'block';
      } else {
        card.style.display = 'none';
      }
    });
  });
});

// Cargar estadísticas
async function loadStatistics() {
  try {
    const response = await fetch('api/admin_statistics.php');
    const result = await response.json();
    
    if (result.success) {
      document.getElementById('totalUsers').textContent = result.data.total_users;
      document.getElementById('totalVisits').textContent = result.data.total_visits;
      document.getElementById('totalReviews').textContent = result.data.total_reviews;
      document.getElementById('totalPhotos').textContent = result.data.total_photos;
    }
  } catch (error) {
    console.error('Error al cargar estadísticas:', error);
  }
}

// Cargar usuarios
async function loadUsers() {
  const container = document.getElementById('usersContainer');
  container.innerHTML = '<p style="text-align: center; padding: 40px; color: var(--muted);">Cargando usuarios...</p>';
  
  try {
    const response = await fetch('api/admin_get_users.php');
    const result = await response.json();
    
    if (result.success && result.data.length > 0) {
      container.innerHTML = result.data.map(user => {
        const initial = user.nombre_usuario.charAt(0).toUpperCase();
        const isAdmin = user.es_admin == 1;
        
        return `
          <div class="user-row" data-user-id="${user.id}">
            <div class="user-avatar">${initial}</div>
            <div class="user-info">
              <h4>${user.nombre_usuario}</h4>
              <p>${user.correo_electronico}</p>
            </div>
            <div class="user-stats">
              <strong>${user.paises_visitados}</strong>
              Países
            </div>
            <div class="user-stats">
              <strong>${user.total_resenas}</strong>
              Reseñas
            </div>
            <div class="user-stats">
              <strong>${user.total_fotos}</strong>
              Fotos
            </div>
            <div style="display: flex; flex-direction: column; gap: 8px; align-items: flex-end;">
              ${isAdmin ? '<span class="badge badge--admin">👑 Admin</span>' : '<span class="badge badge--user">Usuario</span>'}
              <div class="user-actions">
                <button class="action-btn action-btn--primary" onclick="visitUserMap(${user.id})" title="Ver mapa">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                  </svg>
                </button>
                ${!isAdmin ? `
                  <button class="action-btn action-btn--danger" onclick="deleteUser(${user.id}, '${user.nombre_usuario}')" title="Eliminar usuario">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <polyline points="3 6 5 6 21 6"></polyline>
                      <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    </svg>
                  </button>
                ` : ''}
              </div>
            </div>
          </div>
        `;
      }).join('');
      
      container.dataset.loaded = 'true';
    } else {
      container.innerHTML = '<p style="text-align: center; padding: 40px; color: var(--muted);">No hay usuarios registrados</p>';
    }
  } catch (error) {
    console.error('Error al cargar usuarios:', error);
    container.innerHTML = '<p style="text-align: center; padding: 40px; color: #ff3b5c;">Error al cargar usuarios</p>';
  }
}

// Cargar reseñas
async function loadReviews() {
  const container = document.getElementById('reviewsContainer');
  container.innerHTML = '<p style="text-align: center; padding: 40px; color: var(--muted);">Cargando reseñas...</p>';
  
  try {
    const response = await fetch('api/admin_get_reviews.php');
    const result = await response.json();
    
    if (result.success && result.data.length > 0) {
      container.innerHTML = result.data.map(review => {
        const initial = review.nombre_usuario.charAt(0).toUpperCase();
        const stars = '★'.repeat(review.puntuacion) + '☆'.repeat(5 - review.puntuacion);
        
        return `
          <div class="review-card" data-review-id="${review.id}">
            <div class="review-card__header">
              <div class="review-card__user">
                <div class="review-card__avatar">${initial}</div>
                <div>
                  <div class="review-card__username">${review.nombre_usuario}</div>
                  <div class="review-card__country">📍 ${review.pais_nombre}</div>
                </div>
              </div>
              <div class="review-card__rating">${stars}</div>
            </div>
            <h3 class="review-card__title">${review.titulo}</h3>
            <p class="review-card__content">${review.contenido}</p>
            <div class="review-card__footer">
              <span class="review-card__date">${review.fecha_formateada}</span>
              <button class="action-btn action-btn--danger" onclick="deleteReview(${review.id}, '${review.titulo}')" title="Eliminar reseña">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="3 6 5 6 21 6"></polyline>
                  <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                </svg>
              </button>
            </div>
          </div>
        `;
      }).join('');
      
      container.dataset.loaded = 'true';
    } else {
      container.innerHTML = '<p style="text-align: center; padding: 40px; color: var(--muted);">No hay reseñas publicadas</p>';
    }
  } catch (error) {
    console.error('Error al cargar reseñas:', error);
    container.innerHTML = '<p style="text-align: center; padding: 40px; color: #ff3b5c;">Error al cargar reseñas</p>';
  }
}

// Cargar fotos
async function loadPhotos() {
  const container = document.getElementById('photosContainer');
  container.innerHTML = '<p style="text-align: center; padding: 40px; color: var(--muted);">Cargando fotos...</p>';
  
  try {
    const response = await fetch('api/admin_get_photos.php');
    const result = await response.json();
    
    if (result.success && result.data.length > 0) {
      container.innerHTML = result.data.map(photo => {
        const initial = photo.nombre_usuario.charAt(0).toUpperCase();
        
        return `
          <div class="photo-card" data-photo-id="${photo.id}">
            <img src="${photo.imagen_url}" alt="${photo.descripcion || 'Foto'}" class="photo-card__image">
            <div class="photo-card__overlay">
              <div class="photo-card__user">
                <div class="photo-card__avatar">${initial}</div>
                ${photo.nombre_usuario}
              </div>
              <button class="action-btn action-btn--danger" onclick="deletePhoto(${photo.id}, '${photo.pais_nombre}')" title="Eliminar foto" style="background: rgba(0, 0, 0, 0.6); backdrop-filter: blur(10px);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="3 6 5 6 21 6"></polyline>
                  <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                </svg>
              </button>
            </div>
            <div class="photo-card__info">
              <h4 class="photo-card__country">📍 ${photo.pais_nombre}</h4>
              ${photo.descripcion ? `<p class="photo-card__desc">${photo.descripcion}</p>` : ''}
              <div class="photo-card__footer">
                <div class="photo-card__likes">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" stroke="none">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                  </svg>
                  ${photo.total_likes}
                </div>
                <span class="review-card__date">${photo.fecha_formateada}</span>
              </div>
            </div>
          </div>
        `;
      }).join('');
      
      container.dataset.loaded = 'true';
    } else {
      container.innerHTML = '<p style="text-align: center; padding: 40px; color: var(--muted);">No hay fotos publicadas</p>';
    }
  } catch (error) {
    console.error('Error al cargar fotos:', error);
    container.innerHTML = '<p style="text-align: center; padding: 40px; color: #ff3b5c;">Error al cargar fotos</p>';
  }
}

// Visitar mapa de usuario
function visitUserMap(userId) {
  window.location.href = `map.php?view_user=${userId}`;
}

// Eliminar usuario
function deleteUser(userId, username) {
  showConfirmDialog(
    '¿Eliminar usuario?',
    `¿Estás seguro de que quieres eliminar al usuario "${username}"? Esta acción no se puede deshacer y eliminará todas sus fotos, reseñas y datos.`,
    async () => {
      try {
        const response = await fetch('api/admin_delete_user.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ user_id: userId })
        });
        
        const result = await response.json();
        
        if (result.success) {
          // Eliminar de la vista
          const row = document.querySelector(`.user-row[data-user-id="${userId}"]`);
          if (row) {
            row.style.animation = 'fadeOut 0.3s ease';
            setTimeout(() => row.remove(), 300);
          }
          
          // Recargar estadísticas
          loadStatistics();
          
          showNotification('Usuario eliminado correctamente', 'success');
        } else {
          showNotification('Error al eliminar usuario: ' + result.message, 'error');
        }
      } catch (error) {
        console.error('Error:', error);
        showNotification('Error al conectar con el servidor', 'error');
      }
    }
  );
}

// Eliminar reseña
function deleteReview(reviewId, title) {
  showConfirmDialog(
    '¿Eliminar reseña?',
    `¿Estás seguro de que quieres eliminar la reseña "${title}"?`,
    async () => {
      try {
        const response = await fetch('api/admin_delete_review.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ review_id: reviewId })
        });
        
        const result = await response.json();
        
        if (result.success) {
          const card = document.querySelector(`.review-card[data-review-id="${reviewId}"]`);
          if (card) {
            card.style.animation = 'fadeOut 0.3s ease';
            setTimeout(() => card.remove(), 300);
          }
          
          loadStatistics();
          showNotification('Reseña eliminada correctamente', 'success');
        } else {
          showNotification('Error al eliminar reseña: ' + result.message, 'error');
        }
      } catch (error) {
        console.error('Error:', error);
        showNotification('Error al conectar con el servidor', 'error');
      }
    }
  );
}

// Eliminar foto
function deletePhoto(photoId, country) {
  showConfirmDialog(
    '¿Eliminar foto?',
    `¿Estás seguro de que quieres eliminar esta foto de ${country}?`,
    async () => {
      try {
        const response = await fetch('api/admin_delete_photo.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ photo_id: photoId })
        });
        
        const result = await response.json();
        
        if (result.success) {
          const card = document.querySelector(`.photo-card[data-photo-id="${photoId}"]`);
          if (card) {
            card.style.animation = 'fadeOut 0.3s ease';
            setTimeout(() => card.remove(), 300);
          }
          
          loadStatistics();
          showNotification('Foto eliminada correctamente', 'success');
        } else {
          showNotification('Error al eliminar foto: ' + result.message, 'error');
        }
      } catch (error) {
        console.error('Error:', error);
        showNotification('Error al conectar con el servidor', 'error');
      }
    }
  );
}

// Mostrar diálogo de confirmación
function showConfirmDialog(title, message, onConfirm) {
  // Crear modal si no existe
  let modal = document.getElementById('confirmModal');
  if (!modal) {
    modal = document.createElement('div');
    modal.id = 'confirmModal';
    modal.className = 'confirm-modal';
    modal.innerHTML = `
      <div class="confirm-modal__overlay" onclick="closeConfirmDialog()"></div>
      <div class="confirm-modal__content">
        <div class="confirm-modal__icon">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
            <line x1="12" y1="9" x2="12" y2="13"></line>
            <line x1="12" y1="17" x2="12.01" y2="17"></line>
          </svg>
        </div>
        <h3 class="confirm-modal__title"></h3>
        <p class="confirm-modal__message"></p>
        <div class="confirm-modal__actions">
          <button class="confirm-modal__btn confirm-modal__btn--cancel" onclick="closeConfirmDialog()">Cancelar</button>
          <button class="confirm-modal__btn confirm-modal__btn--confirm">Eliminar</button>
        </div>
      </div>
    `;
    document.body.appendChild(modal);
  }
  
  modal.querySelector('.confirm-modal__title').textContent = title;
  modal.querySelector('.confirm-modal__message').textContent = message;
  
  const confirmBtn = modal.querySelector('.confirm-modal__btn--confirm');
  confirmBtn.onclick = () => {
    closeConfirmDialog();
    onConfirm();
  };
  
  modal.style.display = 'flex';
}

function closeConfirmDialog() {
  const modal = document.getElementById('confirmModal');
  if (modal) {
    modal.style.display = 'none';
  }
}

// Mostrar notificación
function showNotification(message, type = 'info') {
  const notification = document.createElement('div');
  notification.style.cssText = `
    position: fixed;
    top: 80px;
    right: 20px;
    padding: 16px 24px;
    background: ${type === 'success' ? 'rgba(76, 175, 80, 0.95)' : 'rgba(255, 59, 92, 0.95)'};
    color: white;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
    z-index: 4000;
    animation: slideIn 0.3s ease;
    font-size: 14px;
    font-weight: 600;
  `;
  notification.textContent = message;
  document.body.appendChild(notification);
  
  setTimeout(() => {
    notification.style.animation = 'fadeOut 0.3s ease';
    setTimeout(() => notification.remove(), 300);
  }, 3000);
}

// Añadir animaciones CSS
const style = document.createElement('style');
style.textContent = `
  @keyframes fadeOut {
    from { opacity: 1; transform: scale(1); }
    to { opacity: 0; transform: scale(0.9); }
  }
  @keyframes slideIn {
    from { transform: translateX(400px); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
  }
`;
document.head.appendChild(style);
