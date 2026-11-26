/**
 * ScalePixel TypeScript Client SDK - Drop-in integration for React Native & Mobile Apps
 */

export interface ScalePixelResponse {
  status: 'success' | 'error';
  message: string;
  data?: {
    filename: string;
    download_url: string;
    original_size_kb: number;
    compressed_size_kb: number;
    saved_bytes: number;
    saved_percent: number;
    original_dimensions: string;
    new_dimensions: string;
    format: string;
    mode: string;
    execution_time_ms: number;
  };
  code?: string;
}

export class ScalePixelClient {
  private apiUrl: string;
  private sourceKey: string;
  private secretKey: string;

  constructor(
    apiUrl: string = 'http://localhost/ScalePixel/api/v1',
    sourceKey: string = 'sp_live_pk_scalepixel_starter',
    secretKey: string = 'sp_live_sk_a92d8f1e4c7b30129481bfe48102a991'
  ) {
    this.apiUrl = apiUrl.replace(/\/$/, '');
    this.sourceKey = sourceKey;
    this.secretKey = secretKey;
  }

  /**
   * Compress Provider Profile Avatar (600x600 JPEG @ 80%)
   * Output filename: provider_{id}_{YYYY-MM-DD_HH-mm}.jpg
   */
  async compressAvatar(
    fileUri: string,
    providerId: number | string,
    includeSeconds: boolean = true
  ): Promise<ScalePixelResponse> {
    const formData = new FormData();
    const filename = fileUri.split('/').pop() || 'avatar.jpg';

    formData.append('file', {
      uri: fileUri,
      name: filename,
      type: 'image/jpeg',
    } as any);

    formData.append('mode', 'avatar');
    formData.append('prefix', `provider_${providerId}`);
    formData.append('include_seconds', includeSeconds ? '1' : '0');
    formData.append('format', 'jpeg');
    formData.append('quality', '80');
    formData.append('width', '600');
    formData.append('height', '600');
    formData.append('fit', 'cover');

    return this.postMultipart('/compress.php', formData);
  }

  /**
   * Compress Shop / Garage / Showcase Photo (1080px WebP @ 75%)
   */
  async compressShowcase(
    fileUri: string,
    providerId: number | string
  ): Promise<ScalePixelResponse> {
    const formData = new FormData();
    const filename = fileUri.split('/').pop() || 'shop.jpg';

    formData.append('file', {
      uri: fileUri,
      name: filename,
      type: 'image/jpeg',
    } as any);

    formData.append('mode', 'showcase');
    formData.append('prefix', `shop_${providerId}`);
    formData.append('format', 'webp');
    formData.append('quality', '75');
    formData.append('width', '1080');

    return this.postMultipart('/compress.php', formData);
  }

  /**
   * Universal format conversion
   */
  async convertFormat(
    fileUri: string,
    toFormat: 'webp' | 'jpeg' | 'png' | 'avif' | 'ico',
    quality: number = 85
  ): Promise<ScalePixelResponse> {
    const formData = new FormData();
    const filename = fileUri.split('/').pop() || 'image.jpg';

    formData.append('file', {
      uri: fileUri,
      name: filename,
      type: 'image/jpeg',
    } as any);

    formData.append('to', toFormat);
    formData.append('quality', String(quality));

    return this.postMultipart('/convert.php', formData);
  }

  private async postMultipart(endpoint: string, formData: FormData): Promise<ScalePixelResponse> {
    try {
      const response = await fetch(`${this.apiUrl}${endpoint}`, {
        method: 'POST',
        headers: {
          'X-ScalePixel-Key': this.sourceKey,
          'X-ScalePixel-Secret': this.secretKey,
          'Authorization': `Bearer ${this.secretKey}`,
        },
        body: formData,
      });

      const json = await response.json();
      return json;
    } catch (error: any) {
      return {
        status: 'error',
        message: error?.message || 'Failed to connect to ScalePixel API service',
      };
    }
  }
}
