def call() {
    echo "Running SonarQube analysis..."
    withSonarQubeEnv('SonarQubeServer') {
        // Execute SonarQube Scanner
        sh "sonar-scanner"
    }
}