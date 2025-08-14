# Amazon Linux 2 based image compatible with AWS Lambda
FROM public.ecr.aws/lambda/provided:al2

# Install PostgreSQL development libraries and build tools
RUN yum install -y \
    postgresql-devel \
    gcc \
    make

# Download and compile PHP 8.2 with pdo_pgsql extension
RUN curl -fsSL https://www.php.net/distributions/php-8.2.19.tar.gz | tar xz
WORKDIR /php-8.2.19

# Configure and compile PHP with pdo_pgsql
RUN ./configure \
    --prefix=/opt/php \
    --with-pdo-pgsql \
    --enable-shared=no \
    --enable-static=yes \
    --disable-cgi \
    --disable-fpm \
    --disable-cli

RUN make -j$(nproc)
RUN make install

# Create the layer structure
RUN mkdir -p /opt/layer/lib/php/8.2
RUN cp -r /opt/php/lib/php/extensions/* /opt/layer/lib/php/8.2/

# Create the final layer archive structure
WORKDIR /opt/layer
