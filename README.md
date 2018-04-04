# hiding-text-scan-herbarium

Process to study how to hide text information on scanned herbarium

How we proceed : 
We have chosen randomly 100 herbarium scans. 
These files are manually edited, a human expert draw with a mouse a few rectangles where he see important data which are to be hidden
the rectangles coordonates are stored (xml, json) in a database

we experiment with two differents process to locate and hide data
first using an OCR, which export hOCR files
second using OpenCV, with a script whcih try to find "rectangles"
the rectangles, determined with these process are then colored in white

Another tools compute the succes rate, comparing the white area with the human chosen "important rectangles"

----
Installation:
Create a "connexion-data.php" file by locating the sample file named "connexion-data-sample.php"

