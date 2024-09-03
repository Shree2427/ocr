import sys
from rapid_latex_ocr import LatexOCR
from pylatexenc.latex2text import LatexNodes2Text
import mysql.connector
import datetime

# Initialize the OCR model
model = LatexOCR()

# Get the image path from command-line arguments
img_path = sys.argv[1]
file_name = img_path.split("\\")[-1]

# Read the image file
with open(img_path, "rb") as f:
    data = f.read()

# Process the image with the OCR model
res, elapse = model(data)

# Convert LaTeX to plain text
plain_text = LatexNodes2Text().latex_to_text(res)

# Get current timestamp
current_time = datetime.datetime.now()

# Connect to MySQL database (adjust connection details as needed)
db_connection = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="ocr_db"
)

cursor = db_connection.cursor()

# Insert result into ocr_results table
insert_query = """
    INSERT INTO ocr_results (file_name, extracted_text, created_at, updated_at)
    VALUES (%s, %s, %s, %s)
"""
data_to_insert = (file_name, plain_text, current_time, current_time)
cursor.execute(insert_query, data_to_insert)

# Commit the transaction
db_connection.commit()

# Close the connection
cursor.close()
db_connection.close()

# Print the result and elapsed time
print({
    "latex_result": res,
    "plain_text": plain_text,
    "elapsed_time": elapse
})
