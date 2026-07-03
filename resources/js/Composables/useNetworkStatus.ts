import { ref, onMounted, onUnmounted } from 'vue';
import { Network } from '@capacitor/network';

export function useNetworkStatus() {
  const isConnected = ref(true);
  const connectionType = ref('unknown');

  const updateConnectionStatus = async () => {
    const status = await Network.getStatus();
    isConnected.value = status.connected;
    connectionType.value = status.connectionType;
  };

  onMounted(async () => {
    await updateConnectionStatus();

    Network.addListener('networkStatusChange', (status) => {
      isConnected.value = status.connected;
      connectionType.value = status.connectionType;
    });
  });

  onUnmounted(() => {
    Network.removeAllListeners();
  });

  return { isConnected, connectionType, refresh: updateConnectionStatus };
}
