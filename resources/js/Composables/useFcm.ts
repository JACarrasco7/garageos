import { PushNotifications } from '@capacitor/push-notifications'
import { router } from '@inertiajs/vue3'

export function useFcm() {
  const registerFcm = async () => {
    const permission = await PushNotifications.requestPermissions()

    if (permission.receive === 'granted') {
      await PushNotifications.register()
    }
  }

  const addListeners = () => {
    PushNotifications.addListener('registration', (token) => {
      router.post(route('fcm-token.update'), {
        fcm_token: token.value,
      })
    })

    PushNotifications.addListener('pushNotificationReceived', (msg) => {
      console.log('Push received', msg)
    })
  }

  return { registerFcm, addListeners }
}
