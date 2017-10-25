#compute the hocr file for the img given in parameter
IMGDIR=/home/hidingzone/htdocs/mesure_efficacite/
convert -type Grayscale  $IMGDIR/imagesrecues/$1 /tmp/img_nb.tif

tesseract /tmp/img_nb.tif $1_ hocr

mv $1_.hocr $IMGDIR/hocr
rm /tmp/img_nb.tif
echo "fait " $1

