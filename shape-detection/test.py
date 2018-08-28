# USAGE
# python detect_shapes.py --image shapes_and_colors.png

# import the necessary packages
from pyimagesearch.shapedetector import ShapeDetector
import argparse
import imutils
import cv2

#numpy pour recuperer list de points
import numpy as np



# construct the argument parse and parse the arguments
ap = argparse.ArgumentParser()
ap.add_argument("-i", "--image", required=True,
	help="path to the input image")
args = vars(ap.parse_args())

# load the image and resize it to a smaller factor so that
# the shapes can be approximated better
image = cv2.imread(args["image"])
resized = imutils.resize(image, width=700)
ratio = image.shape[0] / float(resized.shape[0])

# convert the resized image to grayscale, blur it slightly,
# and threshold it
gray = cv2.cvtColor(resized, cv2.COLOR_BGR2GRAY)
blurred = cv2.GaussianBlur(gray, (5, 5), 0)
thresh = cv2.threshold(blurred, 60, 255, cv2.THRESH_BINARY)[1]

#edges = cv2.Canny(gray,30,100)
thresh = cv2.Canny(gray,30,100)
cv2.imwrite("canny.jpg",thresh)

# find contours in the thresholded image and initialize the
# shape detector

cnts, hierarchy = cv2.findContours(thresh.copy(), cv2.RETR_TREE, cv2.CHAIN_APPROX_SIMPLE)
sd = ShapeDetector()

# Draws contours
for c in cnts:
    if cv2.contourArea(c) < 3000:
        continue

    (x, y, w, h) = cv2.boundingRect(c)
    cv2.rectangle(image, (x,y), (x+w,y+h), (0, 255, 0), 2)

    ## BEGIN - draw rotated rectangle
    rect = cv2.minAreaRect(c)
    box = cv2.boxPoints(rect)
    box = np.int0(box)
    cv2.drawContours(image,[box],0,(0,191,255),2)
    ## END - draw rotated rectangle

cv2.imwrite('out.png', image)

