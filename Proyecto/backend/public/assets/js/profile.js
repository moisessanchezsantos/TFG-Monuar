// Profile Management JavaScript

document.addEventListener('DOMContentLoaded', function() {
  // Cargar estadísticas del usuario
  loadUserStats();
  
  // Formulario de cambio de contraseña
  const changePasswordForm = document.getElementById('changePasswordForm');
  const newPasswordInput = document.getElementById('newPassword');
  const confirmPasswordInput = document.getElementById('confirmPassword');
  const passwordStrength = document.getElementById('passwordStrength');
  
  // Indicador de fuerza de contraseña
  newPasswordInput.addEventListener('input', function() {
    const password = this.value;
    
    if (password.length === 0) {
      passwordStrength.classList.remove('active', 'weak', 'medium', 'strong');
      return;
    }
    
    passwordStrength.classList.add('active');
    
    // Calcular fuerza
    let strength = 0;
    if (password.length >= 6) strength++;
    if (password.length >= 10) strength++;
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
    if (/\d/.test(password)) strength++;
    if (/[^a-zA-Z\d]/.test(password)) strength++;
    
    passwordStrength.classList.remove('weak', 'medium', 'strong');
    if (strength <= 2) {
      passwordStrength.classList.add('weak');
    } else if (strength <= 3) {
      passwordStrength.classList.add('medium');
    } else {
      passwordStrength.classList.add('strong');
    }
  });
  
  // Validación de confirmación de contraseña
  confirmPasswordInput.addEventListener('input', function() {
    if (this.value !== newPasswordInput.value) {
      this.setCustomValidity('Las contraseñas no coinciden');
    } else {
      this.setCustomValidity('');
    }
  });
  
  newPasswordInput.addEventListener('input', function() {
    if (confirmPasswordInput.value && confirmPasswordInput.value !== this.value) {
      confirmPasswordInput.setCustomValidity('Las contraseñas no coinciden');
    } else {
      confirmPasswordInput.setCustomValidity('');
    }
  });
  
  // Submit cambio de contraseña
  changePasswordForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle></svg> Cambiando...';
    
    const formData = {
      current_password: document.getElementById('currentPassword').value,
      new_password: newPasswordInput.value,
      confirm_password: confirmPasswordInput.value
    };
    
    try {
      const response = await fetch('api/change_password.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData)
      });
      
      const result = await response.json();
      
      if (result.success) {
        showNotification('Contraseña cambiada correctamente', 'success');
        this.reset();
        passwordStrength.classList.remove('active', 'weak', 'medium', 'strong');
      } else {
        showNotification(result.message || 'Error al cambiar la contraseña', 'error');
      }
    } catch (error) {
      console.error('Error:', error);
      showNotification('Error al conectar con el servidor', 'error');
    } finally {
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalText;
    }
  });
  
  // Formulario de actualizar perfil
  const updateProfileForm = document.getElementById('updateProfileForm');
  updateProfileForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle></svg> Guardando...';
    
    const formData = {
      username: document.getElementById('username').value,
      email: document.getElementById('email').value
    };
    
    try {
      const response = await fetch('api/update_profile.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData)
      });
      
      const result = await response.json();
      
      if (result.success) {
        showNotification('Perfil actualizado correctamente', 'success');
        // Actualizar el nombre en la cabecera
        document.querySelector('.profile-username').textContent = formData.username;
        document.querySelector('.profile-email').textContent = formData.email;
      } else {
        showNotification(result.message || 'Error al actualizar el perfil', 'error');
      }
    } catch (error) {
      console.error('Error:', error);
      showNotification('Error al conectar con el servidor', 'error');
    } finally {
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalText;
    }
  });
  
  // Cambiar avatar
  const editAvatarBtn = document.getElementById('editAvatarBtn');
  const avatarInput = document.getElementById('avatarInput');
  
  editAvatarBtn.addEventListener('click', function() {
    avatarInput.click();
  });
  
  avatarInput.addEventListener('change', async function() {
    if (!this.files || !this.files[0]) return;
    
    const file = this.files[0];
    
    // Validar tamaño (max 5MB)
    if (file.size > 5 * 1024 * 1024) {
      showNotification('La imagen no puede superar 5MB', 'error');
      return;
    }
    
    // Validar tipo
    if (!file.type.match(/^image\/(jpeg|jpg|png|webp)$/)) {
      showNotification('Formato de imagen no válido. Usa JPG, PNG o WEBP', 'error');
      return;
    }
    
    const formData = new FormData();
    formData.append('avatar', file);
    
    try {
      const response = await fetch('api/upload_avatar.php', {
        method: 'POST',
        body: formData
      });
      
      const result = await response.json();
      
      if (result.success) {
        showNotification('Avatar actualizado correctamente', 'success');
        
        // Actualizar la vista del avatar
        const currentAvatar = document.getElementById('currentAvatar');
        if (currentAvatar.tagName === 'IMG') {
          currentAvatar.src = result.avatar_url + '?t=' + Date.now();
        } else {
          // Es un placeholder, reemplazar con imagen
          const img = document.createElement('img');
          img.src = result.avatar_url;
          img.alt = 'Avatar';
          img.className = 'profile-avatar-img';
          img.id = 'currentAvatar';
          currentAvatar.parentNode.replaceChild(img, currentAvatar);
        }
      } else {
        showNotification(result.message || 'Error al subir el avatar', 'error');
      }
    } catch (error) {
      console.error('Error:', error);
      showNotification('Error al subir la imagen', 'error');
    }
  });
  
  // Eliminar cuenta
  const deleteAccountBtn = document.getElementById('deleteAccountBtn');
  deleteAccountBtn.addEventListener('click', function() {
    document.getElementById('deleteAccountModal').style.display = 'flex';
    document.getElementById('deletePasswordConfirm').value = '';
    document.getElementById('deletePasswordConfirm').focus();
  });
  
  const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
  confirmDeleteBtn.addEventListener('click', async function() {
    const password = document.getElementById('deletePasswordConfirm').value;
    
    if (!password) {
      showNotification('Debes ingresar tu contraseña', 'error');
      return;
    }
    
    this.disabled = true;
    this.textContent = 'Eliminando...';
    
    try {
      const response = await fetch('api/delete_account.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ password })
      });
      
      const result = await response.json();
      
      if (result.success) {
        showNotification('Cuenta eliminada. Redirigiendo...', 'success');
        setTimeout(() => {
          window.location.href = 'logout.php';
        }, 2000);
      } else {
        showNotification(result.message || 'Error al eliminar la cuenta', 'error');
        this.disabled = false;
        this.textContent = 'Eliminar cuenta';
      }
    } catch (error) {
      console.error('Error:', error);
      showNotification('Error al conectar con el servidor', 'error');
      this.disabled = false;
      this.textContent = 'Eliminar cuenta';
    }
  });
});

// Cerrar modal de eliminar cuenta
function closeDeleteModal() {
  document.getElementById('deleteAccountModal').style.display = 'none';
}

// Cargar estadísticas del usuario
async function loadUserStats() {
  const container = document.getElementById('userStats');
  
  try {
    const response = await fetch('api/get_user_stats.php');
    const result = await response.json();
    
    if (result.success) {
      const stats = result.data;
      container.innerHTML = `
        <div class="stat-item">
          <div class="stat-item__icon">🌍</div>
          <h4 class="stat-item__value">${stats.paises_visitados}</h4>
          <p class="stat-item__label">Países visitados</p>
        </div>
        <div class="stat-item">
          <div class="stat-item__icon">📝</div>
          <h4 class="stat-item__value">${stats.total_resenas}</h4>
          <p class="stat-item__label">Reseñas</p>
        </div>
        <div class="stat-item">
          <div class="stat-item__icon">📷</div>
          <h4 class="stat-item__value">${stats.total_fotos}</h4>
          <p class="stat-item__label">Fotos compartidas</p>
        </div>
        <div class="stat-item">
          <div class="stat-item__icon">❤️</div>
          <h4 class="stat-item__value">${stats.total_likes_recibidos}</h4>
          <p class="stat-item__label">Likes recibidos</p>
        </div>
        <div class="stat-item">
          <div class="stat-item__icon">👥</div>
          <h4 class="stat-item__value">${stats.total_seguidores}</h4>
          <p class="stat-item__label">Seguidores</p>
        </div>
        <div class="stat-item">
          <div class="stat-item__icon">➡️</div>
          <h4 class="stat-item__value">${stats.total_siguiendo}</h4>
          <p class="stat-item__label">Siguiendo</p>
        </div>
      `;
      renderUserConnections('followingList', 'followingCountPill', stats.usuarios_siguiendo, 'Todavía no sigues a ningún usuario.');
      renderUserConnections('followersList', 'followersCountPill', stats.seguidores, 'Todavía nadie sigue tu perfil.');
    } else {
      container.innerHTML = '<p style="text-align: center; padding: 20px; color: var(--muted);">Error al cargar estadísticas</p>';
      renderUserConnections('followingList', 'followingCountPill', [], 'No se pudo cargar la lista.');
      renderUserConnections('followersList', 'followersCountPill', [], 'No se pudo cargar la lista.');
    }
  } catch (error) {
    console.error('Error:', error);
    container.innerHTML = '<p style="text-align: center; padding: 20px; color: var(--muted);">Error al cargar estadísticas</p>';
    renderUserConnections('followingList', 'followingCountPill', [], 'No se pudo cargar la lista.');
    renderUserConnections('followersList', 'followersCountPill', [], 'No se pudo cargar la lista.');
  }
}

function renderUserConnections(listId, counterId, users, emptyMsg) {
  const list    = document.getElementById(listId);
  const counter = document.getElementById(counterId);
  if (!list || !counter) return;

  const safeUsers = Array.isArray(users) ? users : [];
  counter.textContent = safeUsers.length;

  if (safeUsers.length === 0) {
    list.innerHTML = `<p class="profile-social-empty">${emptyMsg}</p>`;
    return;
  }

  list.innerHTML = safeUsers.map(u => `
    <a class="profile-social-item" href="map.php?view_user=${u.id}">
      <div class="profile-social-item__avatar">${u.nombre_usuario.charAt(0).toUpperCase()}</div>
      <div class="profile-social-item__content">
        <strong>${u.nombre_usuario}</strong>
        <span>${u.email}</span>
      </div>
      <span class="profile-social-item__cta">Ver mapa</span>
    </a>
  `).join('');
}

// Mostrar notificación
function showNotification(message, type = 'info') {
  const notification = document.createElement('div');
  notification.className = `notification notification--${type}`;
  
  const icon = type === 'success' ? '✓' : type === 'error' ? '✗' : 'ℹ';
  notification.innerHTML = `<strong>${icon}</strong> ${message}`;
  
  document.body.appendChild(notification);
  
  setTimeout(() => {
    notification.style.animation = 'fadeOut 0.3s ease';
    setTimeout(() => notification.remove(), 300);
  }, 3500);
}

// Cerrar modal al hacer clic fuera
document.addEventListener('click', function(e) {
  const modal = document.getElementById('deleteAccountModal');
  if (e.target === modal.querySelector('.confirm-modal__overlay')) {
    closeDeleteModal();
  }
});
