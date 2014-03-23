

v <- c(1.03E6, 1.12E6, 7.99E5, 9.79E5, 1.42E6, 9.63E6, 1.09E7, 1.08E7, 9.93E6, 1.02E7)
a <- -0.0242
samplev <- 4.82E4


df <- data.frame(cbind(a, log10(samplev) - log10(v)))
colnames(df) <- c("t", "ind")

t <- lm(ind ~ t - 1, df)$coefficients[1]
print(t)

t <- lm(ind ~ t - 1, df[1:5,])$coefficients[1]
print(t)

t <- lm(ind ~ t - 1, df[6:10,])$coefficients[1]
print(t)