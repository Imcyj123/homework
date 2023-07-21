import matplotlib.pyplot as plt
from django.http import HttpResponse
import io

def plot_view(request):
    # Create a simple plot (you can customize this according to your data)
    x = [1, 2, 3, 4, 5]
    y = [10, 8, 6, 4, 2]
    plt.plot(x, y)
    plt.xlabel('X-axis')
    plt.ylabel('Y-axis')
    plt.title('Sample Plot')
    # Save the plot to a buffer
    buffer = io.BytesIO()
    plt.savefig(buffer, format='png')
    plt.close()

    # Set the buffer's position to 0, so it can be read from the beginning
    buffer.seek(0)

    # Return the image as a response with the appropriate content type
    return HttpResponse(buffer.getvalue(), content_type='image/png')