from fastapi import FastAPI
from pydantic import BaseModel
from sentence_transformers import SentenceTransformer

app1 = FastAPI()
#sentenceTransformer bib raml transformation ml text ila embeddings grace au modele all minilm mtee ML
model = SentenceTransformer('all-MiniLM-L6-v2')

#textin model bel  Basemodel  :class ml bib pydantic ythabbattlk li données recues ml r=api sous frome text (json )wela le Text in testanna f chaine bech thabat 
class TextIn(BaseModel):
    text: str
@app1.post("/embed")

#def te fonction , yekhou teext verifié y3adih al model yrajalou lvecteur embedding f tableau bel to list()
def embed(data: TextIn):
    vec = model.encode(data.text)
    return {"embedding": vec.tolist()}
