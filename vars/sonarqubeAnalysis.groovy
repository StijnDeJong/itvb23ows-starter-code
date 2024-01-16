def call() {
    def PROJECT_KEY = 'project1'
    def SONAR_SCANNER_IMAGE = 'sonarsource/sonar-scanner-cli:latest'
    def scannerHome = tool 'SonarQube Scanner'

    docker.image(SONAR_SCANNER_IMAGE).inside {
        withSonarQubeEnv('SonarQubeServer') {
            sh "${scannerHome}/bin/sonar-scanner -Dsonar.projectKey=${PROJECT_KEY} -Dsonar.host.url=http://sonarqube:9000"
        }
    }
}
