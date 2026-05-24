# Stage 1: build the fat jar
FROM maven:3.9-eclipse-temurin-21 AS build
WORKDIR /build

COPY pom.xml .
RUN mvn -B -e -ntp dependency:go-offline

COPY src ./src
RUN mvn -B -e -ntp package -DskipTests

# Stage 2: lean runtime
FROM eclipse-temurin:21-jre
WORKDIR /app
COPY --from=build /build/target/*.jar app.jar
EXPOSE 8080
ENTRYPOINT ["java", "-jar", "/app/app.jar"]
