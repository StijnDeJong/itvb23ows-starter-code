def call() {
    def PROJECT_KEY = 'project1'
    def SONAR_SCANNER_IMAGE = 'sonarsource/sonar-scanner-cli:latest'
    def scannerHome = tool 'SonarQube Scanner'
    def SONAR_TOKEN = credentials('SONAR_TOKEN')

    docker.image(SONAR_SCANNER_IMAGE).inside {
        withSonarQubeEnv('SonarQubeServer') {
            withCredentials([string(credentialsId: 'SONAR_TOKEN', variable: 'SONAR_TOKEN')]) {
                sh "${scannerHome}/bin/sonar-scanner -Dsonar.projectKey=${PROJECT_KEY} -Dsonar.host.url=http://host.docker.internal:9000 -Dsonar.login=${SONAR_TOKEN}"
            }
        }
    }
}
