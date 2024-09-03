import sys
from rapid_latex_ocr import LatexOCR
from pylatexenc.latex2text import LatexNodes2Text
import json
import io

# Set the default encoding to UTF-8 for standard output
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')

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

# Prepare the result data
result = {
    "file_name": file_name,
    "latex_result": res,
    "plain_text": plain_text,
    "elapsed_time": elapse
}

# Print the result as JSON
print(json.dumps(result, ensure_ascii=False))
