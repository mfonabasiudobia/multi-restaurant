<template>
    <div class="margin-container notifications-container">
      <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3 mb-4">
        <h4 class="notifications-title">{{ $t('Notifications') }}</h4>
      </div>
  
      <div class="notifications-list">
        <div class="row">
          <div class="col-lg-12">
            <a 
              v-for="notification in notifications" 
              :key="notification.id"
              :href="notification.url"
              class="notification-card"
              :class="{'notification-read': notification.is_read}"
            >
              <div class="notification-content">
                <div class="notification-icon">
                  <!-- <div :class="['icon-wrapper', getIconClass(notification.type)]">
                    <i :class="getNotificationIcon(notification)"></i>
                  </div> -->
                </div>
                <div class="notification-details">
                  <div class="notification-header">
                    <span 
                      class="notification-title"
                      :class="{'read': notification.is_read}"
                    >
                      {{ notification.title }}
                    </span>
                    <span class="notification-time">{{ notification.created_at }}</span>
                  </div>
                  <div class="notification-body">
                    <p class="notification-message" v-html="formatMessage(notification.message || notification.content)"></p>
                  </div>
                </div>
                <div class="notification-actions" @click.stop>
                  <button 
                    @click="deleteNotification(notification.id)" 
                    class="delete-btn"
                    :title="$t('Delete')"
                  >
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </div>
            </a>
            
            <div v-if="notifications.length === 0" class="empty-notifications">
              <i class="bi bi-bell-slash"></i>
              <p>{{ $t('No notifications yet') }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  import { useAuth } from '../stores/AuthStore';
  
  const AuthStore = useAuth();
  
  export default {
    data() {
      return {
        notifications: [],
      };
    },
    created() {
      this.fetchNotifications();
    },
    methods: {
      async fetchNotifications() {
        try {
          const response = await axios.get('/notifications', {
            headers: {
              'Authorization': `Bearer ${AuthStore.token}`,
            }
          });
          console.log(response.data);
          if (response.data.success) {
            this.notifications = response.data.notifications;
          }
        } catch (error) {
          console.error('Error fetching notifications:', error);
        }
      },
      formatMessage(message) {
        if (!message) return '';
        return message.replace(/\n/g, '<br>');
      },
      getNotificationIcon(notification) {
        if (notification.type === 'product') {
          if (notification.icon === 'bi-whatsapp') {
            return 'bi bi-whatsapp text-success';
          }
          return 'bi bi-bag-check-fill';
        }
        if (notification.type === 'order') return 'bi bi-cart-check-fill';
        if (notification.type === 'payment') return 'bi bi-credit-card-fill';
        if (notification.type === 'danger') return 'bi bi-exclamation-triangle-fill';
        if (notification.type === 'success') return 'bi bi-check-circle-fill';
        return 'bi bi-bell-fill';
      },
      getIconClass(type) {
        switch(type) {
          case 'product':
            return 'icon-product';
          case 'order':
            return 'icon-order';
          case 'payment':
            return 'icon-payment';
          case 'danger':
            return 'icon-danger';
          case 'success':
            return 'icon-success';
          default:
            return 'icon-default';
        }
      },
      async deleteNotification(notificationId) {
        try {
          await axios.delete(`/notifications/${notificationId}`, {
            headers: {
              'Authorization': `Bearer ${AuthStore.token}`,
            }
          });
          this.notifications = this.notifications.filter(notification => notification.id !== notificationId);
        } catch (error) {
          console.error('Error deleting notification:', error);
        }
      },
    },
  };
  </script>
  
  <style scoped>
  .notifications-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
  }

  .notifications-title {
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 0;
  }

  .notification-card {
    background: #ffffff;
    border-radius: 16px;
    margin-bottom: 16px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    transition: all 0.3s ease;
    border: 1px solid #eef0f5;
    overflow: hidden;
    text-decoration: none;
    display: block;
    cursor: pointer;
  }

  .notification-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    border-color: #e2e8f0;
  }

  .notification-content {
    display: flex;
    align-items: flex-start;
    padding: 20px;
    gap: 16px;
  }

  .notification-icon {
    flex-shrink: 0;
  }

  .icon-wrapper {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    transition: all 0.3s ease;
  }

  .notification-card:hover .icon-wrapper {
    transform: scale(1.1);
  }

  .icon-product {
    background-color: #e3f2fd;
    color: #1976d2;
  }

  .icon-order {
    background-color: #fff3e0;
    color: #f57c00;
  }

  .icon-payment {
    background-color: #e8eaf6;
    color: #3f51b5;
  }

  .icon-danger {
    background-color: #ffebee;
    color: #d32f2f;
  }

  .icon-success {
    background-color: #e8f5e9;
    color: #2e7d32;
  }

  .icon-default {
    background-color: #f5f5f5;
    color: #616161;
  }

  .notification-details {
    flex-grow: 1;
    min-width: 0;
  }

  .notification-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 8px;
    gap: 12px;
  }

  .notification-title {
    color: #2c3e50;
    font-weight: 600;
    text-decoration: none;
    font-size: 1.1rem;
    margin: 0;
    flex: 1;
    min-width: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .notification-title:hover {
    color: #1976d2;
  }

  .notification-title.read {
    color: #90a4ae;
    font-weight: normal;
  }

  .notification-time {
    font-size: 0.85rem;
    color: #94a3b8;
    white-space: nowrap;
  }

  .notification-body {
    position: relative;
  }

  .notification-message {
    color: #64748b;
    margin: 0;
    font-size: 0.95rem;
    line-height: 1.6;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
  }

  .notification-actions {
    opacity: 0;
    transition: opacity 0.2s ease;
    position: relative;
    z-index: 2;
  }

  .notification-card:hover .notification-actions {
    opacity: 1;
  }

  .notification-card:active {
    transform: translateY(0);
  }

  .delete-btn {
    background: none;
    border: none;
    color: #dc3545;
    padding: 8px;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .delete-btn:hover {
    background-color: #fee2e2;
    transform: scale(1.1);
    color: #ef4444;
  }

  .empty-notifications {
    text-align: center;
    padding: 60px 20px;
    color: #94a3b8;
    background: #f8fafc;
    border-radius: 16px;
    margin: 20px 0;
  }

  .empty-notifications i {
    font-size: 3.5rem;
    margin-bottom: 16px;
    opacity: 0.7;
  }

  .empty-notifications p {
    font-size: 1.1rem;
    margin: 0;
  }

  .notification-read {
    background-color: #f8fafc;
    border-color: #e2e8f0;
  }

  .notification-read .notification-title {
    color: #64748b;
  }

  .notification-read .icon-wrapper {
    opacity: 0.7;
  }

  @keyframes slideIn {
    from {
      opacity: 0;
      transform: translateY(-10px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .notification-card {
    animation: slideIn 0.3s ease-out forwards;
  }
  </style>