def call() {
    echo "Deploying containers..."
    docker.image('docker/compose:latest').inside {
        sh 'docker-compose -f docker/docker-compose.jenkins.yml up -d'
    }
}
