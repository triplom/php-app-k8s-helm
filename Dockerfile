# Use a pinned version for reproducible builds — do not use :latest
FROM bitnami/php-fpm:8.2

# Copy only the application source code into the image
COPY app /app

# Set the working directory
WORKDIR /app

# Expose the PHP-FPM port
EXPOSE 9000

# Health check: validate PHP-FPM configuration
HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 \
  CMD php-fpm -t 2>/dev/null || exit 1
