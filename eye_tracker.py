import cv2
import mediapipe as mp
import pyautogui
import time
import os

pyautogui.FAILSAFE = False

# Constants for tuning (ajustés pour plus de sensibilité)
UP_THRESHOLD = 0.42
DOWN_THRESHOLD = 0.58
SCROLL_SPEED = 120  # Windows scroll ticks

# Check if model exists
model_path = 'face_landmarker.task'
if not os.path.exists(model_path):
    print(f"Erreur: Le modele {model_path} est introuvable.")
    exit(1)

BaseOptions = mp.tasks.BaseOptions
FaceLandmarker = mp.tasks.vision.FaceLandmarker
FaceLandmarkerOptions = mp.tasks.vision.FaceLandmarkerOptions
VisionRunningMode = mp.tasks.vision.RunningMode

options = FaceLandmarkerOptions(
    base_options=BaseOptions(model_asset_path=model_path),
    running_mode=VisionRunningMode.VIDEO)

print("Démarrage du suivi oculaire (Eye Tracking) avec l'API Tasks ! Regardez en haut/bas pour faire défiler.")

with FaceLandmarker.create_from_options(options) as landmarker:
    cap = cv2.VideoCapture(0)
    
    while cap.isOpened():
        success, image = cap.read()
        if not success:
            break

        image = cv2.flip(image, 1)
        rgb_image = cv2.cvtColor(image, cv2.COLOR_BGR2RGB)
        mp_image = mp.Image(image_format=mp.ImageFormat.SRGB, data=rgb_image)
        
        timestamp_ms = int(time.time() * 1000)
        face_landmarker_result = landmarker.detect_for_video(mp_image, timestamp_ms)

        if face_landmarker_result.face_landmarks:
            landmarks = face_landmarker_result.face_landmarks[0]
            
            # Left Eye: Top=159, Bottom=145, Iris Center=473
            eye_top_y = landmarks[159].y
            eye_bottom_y = landmarks[145].y
            iris_y = landmarks[473].y

            eye_height = eye_bottom_y - eye_top_y
            
            if eye_height > 0:
                ratio = (iris_y - eye_top_y) / eye_height
                # print(f"Ratio: {ratio:.2f}") # Debug

                if ratio < UP_THRESHOLD:
                    pyautogui.scroll(SCROLL_SPEED)
                    cv2.putText(image, "Haut", (50, 50), cv2.FONT_HERSHEY_SIMPLEX, 1, (0, 255, 0), 2)
                elif ratio > DOWN_THRESHOLD:
                    pyautogui.scroll(-SCROLL_SPEED)
                    cv2.putText(image, "Bas", (50, 50), cv2.FONT_HERSHEY_SIMPLEX, 1, (0, 0, 255), 2)
                else:
                    cv2.putText(image, "Centre", (50, 50), cv2.FONT_HERSHEY_SIMPLEX, 1, (255, 0, 0), 2)
                
                # Afficher le ratio exact pour aider l'utilisateur
                cv2.putText(image, f"Pos: {ratio:.2f}", (50, 90), cv2.FONT_HERSHEY_SIMPLEX, 0.7, (255, 255, 255), 1)


        cv2.imshow('InnoLearn Eye Tracker', image)

        if cv2.waitKey(5) & 0xFF == ord('q'):
            break

    cap.release()
cv2.destroyAllWindows()

