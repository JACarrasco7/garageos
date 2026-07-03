import { Capacitor } from '@capacitor/core';
import { PushNotifications } from '@capacitor/push-notifications';
import { onMounted } from 'vue';

export function usePushNotifications() {
  const requestPermission = async () => {
    if (Capacitor.getPlatform() === 'web') return true;

    try {
      const permission = await PushNotifications.requestPermissions();
      return permission.receive;
    } catch (error) {
      console.error('Error requesting push permissions:', error);
      return false;
    }
  };

  const listenForNotifications = () => {
    return PushNotifications.addListener('pushNotificationReceived', (notification) => {
      console.log('Notification received:', notification);
    });
  };

  const listenForNotificationResponse = () => {
    return PushNotifications.addListener('pushNotificationActionPerformed', (notification) => {
      console.log('Notification tapped:', notification);
    });
  };

  onMounted(() => {
    if (Capacitor.getPlatform() !== 'web') {
      requestPermission();
      listenForNotifications();
      listenForNotificationResponse();
    }
  });

  return { requestPermission, listenForNotifications, listenForNotificationResponse };
}
