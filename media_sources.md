# Media Sources & Attributions

This document lists the original high-quality Pexels and Unsplash URLs for the media assets used across the Velvet Vogue clothing store website.

## Hero Section Video

- **Ambient Studio Fashion Video**
  - **Source**: Pexels / Mixkit
  - **Pexels ID**: `8533816`
  - **Web URL**: [Pexels Video (8533816)](https://www.pexels.com/video/young-woman-posing-in-fashionable-clothes-8533816/)
  - **Mixkit URL**: [Mixkit Fashion Woman Walking](https://mixkit.co/free-stock-video/woman-in-fashion-clothes-walking-in-a-studio-40487/)

### Video Configuration & Formats
The website supports both **WebM** and **MP4** formats for optimal delivery performance.
To configure the video sources:
1. Open `index.php`.
2. Locate the **HERO SECTION VIDEO & LAYOUT CONFIGURATION** block.
3. Configure the `$hero_video_layout` parameter:
   - `'full'`: Immersive full-bleed background video with dark glassmorphic text box.
   - `'split'`: Classic split-screen layout with the video hosted in a card graphic on the right.
4. **Cloudinary Integration**:
   - Provide your Cloudinary cloud name in `$cloudinary_cloud_name`.
   - Provide your video public ID in `$cloudinary_video_id`.
   - The site will automatically use Cloudinary's dynamic format transcoding (`f_webm`/`f_mp4`) and quality optimization (`q_auto`).
5. **Fallback Sources**:
   - Alternatively, specify direct WebM or MP4 URLs in `$fallback_video_webm` and `$fallback_video_mp4`.

---

## Product Images

The following table lists the 10 products seeded in the database, their local filenames, and their original stock photo sources:

| Product Name | Local Filename | Stock Photo URL | Platform |
| :--- | :--- | :--- | :--- |
| **Classic Trench Coat** | `trench_coat.jpg` | [Trench Coat Photo](https://images.unsplash.com/photo-1591047139829-d91aecb6caea?w=800&q=80) | Unsplash |
| **Silk Slip Dress** | `silk_slip.jpg` | [Silk Slip Dress Photo](https://www.pexels.com/photo/291759/) | Pexels |
| **Tailored Wool Blazer** | `wool_blazer.jpg` | [Wool Blazer Photo](https://www.pexels.com/photo/1342609/) | Pexels |
| **Linen Resort Shirt** | `linen_shirt.jpg` | [Linen Resort Shirt Photo](https://images.unsplash.com/photo-1598033129183-c4f50c736f10?w=800&q=80) | Unsplash |
| **Heavyweight Cotton Tee** | `cotton_tee.jpg` | [Cotton T-shirt Photo](https://www.pexels.com/photo/2294342/) | Pexels |
| **Pleated Dress Trousers** | `pleated_trousers.jpg` | [Pleated Trousers Photo](https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?w=800&q=80) | Unsplash |
| **Leather Crossbody Bag** | `leather_bag.jpg` | [Leather Crossbody Bag Photo](https://www.pexels.com/photo/1152077/) | Pexels |
| **Cashmere Knit Scarf** | `cashmere_scarf.jpg` | [Cashmere Scarf Photo](https://www.pexels.com/photo/375880/) | Pexels |
| **Minimalist Leather Watch** | `leather_watch.jpg` | [Leather Watch Photo](https://www.pexels.com/photo/190819/) | Pexels |
| **Satin Wide-Leg Pants** | `satin_pants.jpg` | [Satin Wide-Leg Pants Photo](https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=800&q=80) | Unsplash |
