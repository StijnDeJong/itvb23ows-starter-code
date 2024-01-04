def call() {
    docker.image('docker/compose:latest').inside {
        sh 'docker-compose up -d'
    }
}
