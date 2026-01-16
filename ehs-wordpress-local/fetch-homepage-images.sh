#!/bin/bash

# Fetch Homepage Images from Pexels API
# This script downloads professional images for the EHS Analytical home page

PEXELS_API_KEY="PYJYwyfEO7NPiJGGOAly5HhPAuTFZLwPfP0sVwhwWHIxkeetzGNXra0W"
IMAGE_DIR="wordpress/wp-content/themes/hello-elementor-child/assets/images"

echo "Fetching images from Pexels API..."

# Fetch hero background - Construction safety/EHS professional
echo "Downloading hero background..."
curl -H "Authorization: $PEXELS_API_KEY" \
  "https://api.pexels.com/v1/search?query=construction+safety+worker&per_page=1&orientation=landscape" \
  | grep -o '"original":"[^"]*"' | head -1 | cut -d'"' -f4 | xargs -I {} \
  curl -o "$IMAGE_DIR/hero-background.jpg" {}

# Fetch about background - Construction safety/EHS professional
echo "Downloading about background..."
curl -H "Authorization: $PEXELS_API_KEY" \
  "https://api.pexels.com/v1/search?query=construction+safety+inspection+hard+hat&per_page=1&orientation=landscape" \
  | grep -o '"original":"[^"]*"' | head -1 | cut -d'"' -f4 | xargs -I {} \
  curl -o "$IMAGE_DIR/about-background.jpg" {}

echo "Images downloaded successfully!"
echo "Hero background: $IMAGE_DIR/hero-background.jpg"
echo "About background: $IMAGE_DIR/about-background.jpg"
