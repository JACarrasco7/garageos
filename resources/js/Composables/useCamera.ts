import { Capacitor } from '@capacitor/core';

export function useCamera() {
  const takePicture = async () => {
    if (Capacitor.getPlatform() === 'web') {
      return webTakePicture();
    }

    try {
      const { Camera, CameraResultType } = await import('@capacitor/camera');
      const image = await Camera.getPhoto({
        quality: 80,
        allowEditing: true,
        resultType: CameraResultType.Uri,
      });
      return image.webPath || image.path;
    } catch (error) {
      console.error('Error taking picture:', error);
      return null;
    }
  };

  const webTakePicture = async () => {
    return new Promise<string>((resolve) => {
      const input = document.createElement('input');
      input.type = 'file';
      input.accept = 'image/*';
      input.onchange = (e) => {
        const file = (e.target as HTMLInputElement).files?.[0];
        if (file) {
          resolve(URL.createObjectURL(file));
        }
      };
      input.click();
    });
  };

  return { takePicture };
}
