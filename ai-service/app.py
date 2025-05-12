from fastapi import FastAPI
#defnir des modeles de donnees avec validation
from pydantic import BaseModel
from sentence_transformers import SentenceTransformer

#sera utlisé pour creer des endpoints des api 
app1 = FastAPI()
#all-MiniLM-L6-v2 modele pre entrainé  utilisé pour encoder le texte en embeddings sémantiques.
model = SentenceTransformer('all-MiniLM-L6-v2')

#def de modeele de donnes Ce modèle est utilisé pour valider les données d'entrée reçues par l'API.
class TextIn(BaseModel):
    text: str
#éfinit un endpoint POST /embed dans l'application FastAPI.
@app1.post("/embed")
#Définit une fonction embed qui prend un objet data de type TextIn en entrée. Cet objet contient le texte à encoder.


def embed(data: TextIn):
    #Utilise le modèle Sentence Transformers pour encoder le texte (data.text) en un vecteur d'embedding sémantique
    vec = model.encode(data.text)
    #Renvoie un objet JSON contenant l'embedding sous forme de liste (vec.tolist()).
    return {"embedding": vec.tolist()}
