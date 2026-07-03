import { Capacitor } from '@capacitor/core';
import { Filesystem } from '@capacitor/filesystem';

export async function optimizeImage(file: File, maxWidth = 1080): Promise<File> {
  if (Capacitor.getPlatform() === 'web') {
    return file;
  }

  const img = new Image();
  const url = URL.createObjectURL(file);
  img.src = url;

  await new Promise((resolve) => (img.onload = resolve));

  const canvas = document.createElement('canvas');
  const ctx = canvas.getContext('2d');

  const ratio = Math.min(img.width / maxWidth, 1);
  canvas.width = img.width * ratio;
  canvas.height = img.height * ratio;

  ctx?.drawImage(img, 0, 0, canvas.width, canvas.height);

  return new Promise((resolve) => {
    canvas.toBlob((blob) => {
      if (blob) {
        const optimizedFile = new File([blob], file.name, { type: 'image/webp' });
        resolve(optimizedFile);
      } else {
        resolve(file);
      }
    }, 'image/webp', 0.8);
  });
}

export async function toWebP(file: File): Promise<File> {
  return optimizeImage(file);
}
