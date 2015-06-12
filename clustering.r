library('MASS')
			#library('praclus')
			.libPaths('/home/administrador/R/x86_64-unknown-linux-gnu-library/3.1')
			library('sna')
			library('ade4')
			library('kernlab')
			library('vegan')
			library('mclust')

			datum<-read.csv("testfile.csv", header=T)
			
			
			
			datum
			etiquetas<-datum$Dato
			datum=datum[-c(1)]
			attach(datum)
			filas=nrow(datum)
			filas=filas*0.45
			#datum=datum[-1]
			#attach(datum)
			#matDatum
			#transposed.frame<-t(datum)
			#transposed.frame
			#datum=transposed.frame[-1]
			#transposed.frame
			#transposed.frame$Dato <- NULL
			#datum<-transposed.frame
			datum
			matDatum=as.matrix(datum)
			matDatum
			

			#dist.Datum<- vegdist(matDatum, method= "jaccard")
			dist.Datum<- vegdist(matDatum, method= "jaccard")
			dist.Datum
			#distDatum
			#clus_1<- hclust(as.dist(matDatum))
			#hclust(dist.Datum)
			clus_1 <-hclust(dist.Datum)
			clus_1$labels<-etiquetas   
			cex=0.5
			pdf("clustering.pdf")
			par(cex=0.3)	
			#plot(clus_1)
			#rect.hclust(clus_1, 5)	
			dev.off()
			filas=ceiling(filas)
			
			data<-cutree(k=filas, clus_1)
			data
			write.table(data, "grupos.txt", sep="\t") 
			write.table(data, "grupos.txt", sep="\t") 

		
			
