import speech_recognition as sr
import pyautogui
import time
import sys

def listen_and_type():
    recognizer = sr.Recognizer()
    
    with sr.Microphone() as source:
        print("Écoute en cours... (Parlez maintenant)")
        # Ajuster le bruit ambiant
        recognizer.adjust_for_ambient_noise(source, duration=1)
        try:
            # Écoute pendant max 5 secondes
            audio = recognizer.listen(source, timeout=5, phrase_time_limit=5)
            print("Traitement de la voix...")
            
            # Reconnaissance via Google (Français)
            text = recognizer.recognize_google(audio, language="fr-FR")
            print(f"Texte détecté : {text}")
            
            # Attendre un tout petit peu pour s'assurer que le focus est sur le champ
            time.sleep(0.5)
            
            # Effacer le contenu actuel (Ctrl+A puis Backspace)
            pyautogui.hotkey('ctrl', 'a')
            pyautogui.press('backspace')
            
            # Taper le nouveau texte
            pyautogui.write(text)
            # Appuyer sur Entrée pour valider la recherche si besoin
            pyautogui.press('enter')
            
        except sr.WaitTimeoutError:
            print("Aucune voix détectée.")
        except sr.UnknownValueError:
            print("Désolé, je n'ai pas compris l'audio.")
        except sr.RequestError as e:
            print(f"Erreur de service; {e}")
        except Exception as e:
            print(f"Erreur inattendue : {e}")

if __name__ == "__main__":
    listen_and_type()
